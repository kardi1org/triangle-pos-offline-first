(function () {
    'use strict';

    const config = window.POS_OFFLINE_CONFIG || {};
    const dbName = 'triangle-pos-offline';
    const dbVersion = 2;
    let dbPromise;

    const money = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value || 0));

    const openDb = () => {
        if (dbPromise) return dbPromise;

        dbPromise = new Promise((resolve, reject) => {
            const request = indexedDB.open(dbName, dbVersion);

            request.onupgradeneeded = () => {
                const db = request.result;
                if (!db.objectStoreNames.contains('meta')) db.createObjectStore('meta');
                if (!db.objectStoreNames.contains('products')) db.createObjectStore('products', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('categories')) db.createObjectStore('categories', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('tables')) db.createObjectStore('tables', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('pendingOrders')) db.createObjectStore('pendingOrders', { keyPath: 'reference' });
                if (!db.objectStoreNames.contains('cart')) db.createObjectStore('cart', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('orders')) db.createObjectStore('orders', { keyPath: 'local_reference' });
            };

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });

        return dbPromise;
    };

    const txStore = async (name, mode = 'readonly') => {
        const db = await openDb();
        return db.transaction(name, mode).objectStore(name);
    };

    const requestToPromise = (request) => new Promise((resolve, reject) => {
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });

    const put = async (store, value, key) => requestToPromise((await txStore(store, 'readwrite')).put(value, key));
    const get = async (store, key) => requestToPromise((await txStore(store)).get(key));
    const del = async (store, key) => requestToPromise((await txStore(store, 'readwrite')).delete(key));
    const clear = async (store) => requestToPromise((await txStore(store, 'readwrite')).clear());
    const all = async (store) => requestToPromise((await txStore(store)).getAll());

    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || config.csrf || '';
    const offlineState = {
        selectedTableIds: [],
        selectedTableNames: [],
        orderType: document.querySelector('input[name="order_type"]')?.value || 'dine_in',
    };
    let appReachable = navigator.onLine;
    let reachabilityCheckedAt = 0;
    let syncInProgress = false;
    const offlineMode = () => !navigator.onLine || !appReachable;

    const registerServiceWorker = () => {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/pos-service-worker.js').catch(() => null);
        }
    };

    const checkAppReachability = async (force = false) => {
        if (!config.offlineDataUrl) {
            appReachable = navigator.onLine;
            return appReachable;
        }

        const now = Date.now();
        if (!force && now - reachabilityCheckedAt < 5000) return appReachable;
        reachabilityCheckedAt = now;

        if (!navigator.onLine) {
            appReachable = false;
            return false;
        }

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 2500);

        try {
            const pingUrl = new URL(config.offlineDataUrl, window.location.origin);
            pingUrl.searchParams.set('ping', '1');
            const response = await fetch(pingUrl.toString(), {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                signal: controller.signal,
                cache: 'no-store',
            });
            appReachable = response.ok;
        } catch (e) {
            appReachable = false;
        } finally {
            clearTimeout(timeout);
        }

        return appReachable;
    };

    const snapshotVisibleProducts = async () => {
        const nodes = document.querySelectorAll('[data-offline-product]');
        for (const node of nodes) {
            try {
                await put('products', JSON.parse(node.dataset.offlineProduct));
            } catch (e) {
                // Ignore malformed product data from stale markup.
            }
        }
    };

    const syncMasterData = async () => {
        if (!config.offlineDataUrl || !(await checkAppReachability(true))) return;

        try {
            const response = await fetch(config.offlineDataUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) return;

            const data = await response.json();
            await put('meta', { ...data, key: 'snapshot' }, 'snapshot');

            for (const product of data.products || []) await put('products', product);
            for (const category of data.categories || []) await put('categories', category);
            for (const table of data.tables || []) await put('tables', table);
            await clear('pendingOrders');
            for (const order of data.pending_orders || []) await put('pendingOrders', order);
            appReachable = true;
        } catch (e) {
            appReachable = false;
            // The last successful snapshot remains available.
        }
    };

    const calculateProductPrice = (product) => {
        const price = Number(product.product_price || 0);
        const tax = Number(product.product_order_tax || 0);

        if (Number(product.product_tax_type) === 1) {
            return {
                price: price + (price * tax / 100),
                unit_price: price,
                product_tax: price * tax / 100,
            };
        }

        if (Number(product.product_tax_type) === 2) {
            return {
                price,
                unit_price: price - (price * tax / 100),
                product_tax: price * tax / 100,
            };
        }

        return { price, unit_price: price, product_tax: 0 };
    };

    const cartItems = async () => all('cart');

    const addProductToCart = async (product) => {
        const existing = await get('cart', Number(product.id));
        const prices = calculateProductPrice(product);
        const qty = existing ? Number(existing.qty || 0) + 1 : 1;

        if (Number(product.product_quantity || 0) > 0 && qty > Number(product.product_quantity)) {
            showOfflineAlert('Stok tidak cukup untuk ' + product.product_name, 'warning');
            return;
        }

        await put('cart', {
            id: Number(product.id),
            name: product.product_name,
            code: product.product_code,
            qty,
            price: prices.price,
            unit_price: prices.unit_price,
            product_tax: prices.product_tax,
            product_discount: 0,
            product_discount_type: 'fixed',
            stock: Number(product.product_quantity || 0),
            unit: product.product_unit,
            variants: existing?.variants || [],
        });

        await renderOfflineCart();
    };

    const findProducts = async (term, limit = 12) => {
        const q = String(term || '').trim().toLowerCase();
        if (!q) return [];

        return (await all('products'))
            .filter((product) => {
                return String(product.product_name || '').toLowerCase().includes(q) ||
                    String(product.product_code || '').toLowerCase().includes(q) ||
                    String(product.barcode || '').toLowerCase() === q;
            })
            .slice(0, limit);
    };

    const renderSearchResults = async (input) => {
        if (!offlineMode()) return;

        const wrapper = input.closest('.position-relative');
        if (!wrapper) return;

        wrapper.querySelectorAll('[data-offline-search-results]').forEach((node) => node.remove());
        const results = await findProducts(input.value);

        if (!input.value.trim()) return;

        const panel = document.createElement('div');
        panel.className = 'card position-absolute mt-1';
        panel.style.cssText = 'z-index: 20;left:0;right:0;border:0;';
        panel.dataset.offlineSearchResults = 'true';

        panel.innerHTML = `
            <div class="card-body shadow">
                ${results.length ? `
                    <ul class="list-group list-group-flush">
                        ${results.map((product) => `
                            <li class="list-group-item list-group-item-action">
                                <a href="#" data-offline-search-select="${product.id}">
                                    ${escapeHtml(product.product_name)} | ${escapeHtml(product.product_code || product.barcode || '')}
                                </a>
                            </li>
                        `).join('')}
                    </ul>
                ` : '<div class="alert alert-warning mb-0">No Product Found....</div>'}
            </div>
        `;

        wrapper.appendChild(panel);
    };

    const renderOfflineProductList = async () => {
        if (!offlineMode()) return;

        const productRow = document.querySelector('[data-offline-product]')?.closest('.row.position-relative');
        if (!productRow) return;

        const products = await all('products');
        productRow.innerHTML = products.slice(0, 30).map((product) => `
            <div data-offline-product='${JSON.stringify(product).replace(/'/g, '&#039;')}'
                class="col-lg-4 col-md-4 col-xl-3 col-sm-6 mb-2" style="cursor: pointer;">
                <div class="card border-0 shadow h-100">
                    <div class="position-relative">
                        <img height="150px" src="${escapeHtml(product.image_url || '')}" class="card-img-top" alt="Product Image">
                    </div>
                    <div class="card-body">
                        <div class="mb-0">
                            <h6 style="font-size: 13px;" class="card-title mb-0">${escapeHtml(product.product_name)}</h6>
                        </div>
                        <p class="card-text font-weight-bold">${money(product.product_price)}</p>
                    </div>
                </div>
            </div>
        `).join('') || '<div class="col-12"><div class="alert alert-warning mb-0">Products Not Found...</div></div>';
    };

    const setQty = async (id, qty) => {
        const item = await get('cart', Number(id));
        if (!item) return;

        item.qty = Math.max(1, Number(qty || 1));
        if (item.stock > 0 && item.qty > item.stock) {
            showOfflineAlert('Stok tidak cukup untuk ' + item.name, 'warning');
            item.qty = item.stock;
        }

        await put('cart', item);
        await renderOfflineCart();
    };

    const removeItem = async (id) => {
        await del('cart', Number(id));
        await renderOfflineCart();
    };

    const calculateTotals = (items) => {
        const subtotal = items.reduce((sum, item) => sum + Number(item.price || 0) * Number(item.qty || 0), 0);
        return {
            subtotal,
            total: subtotal,
            tax_amount: 0,
            discount_amount: 0,
        };
    };

    const renderOfflineCart = async () => {
        if (!offlineMode()) return;

        const list = document.querySelector('.cart-list');
        if (!list) return;

        const items = await cartItems();
        const totals = calculateTotals(items);

        if (!items.length) {
            list.innerHTML = '<div class="text-center text-danger py-3">Please search & select products!</div>';
        } else {
            list.innerHTML = `
                <div class="d-none d-md-flex font-weight-bold text-center border-bottom py-2 bg-light">
                    <div class="col-md-4 text-left">Product</div>
                    <div class="col-md-2">Price</div>
                    <div class="col-md-4">Quantity</div>
                    <div class="col-md-2">Action</div>
                </div>
                ${items.map((item) => `
                    <div class="cart-item border-bottom py-2" data-offline-cart-row="${item.id}">
                        <div class="row align-items-center text-center text-md-left">
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <div class="d-md-none text-muted small font-weight-bold mb-1">Product</div>
                                <strong>${escapeHtml(item.name)}</strong><br>
                                <span class="badge badge-success">${escapeHtml(item.code || '')}</span>
                            </div>
                            <div class="col-6 col-md-2 mb-2 mb-md-0">
                                <div class="d-md-none text-muted small font-weight-bold mb-1">Price</div>
                                ${money(item.price)}
                            </div>
                            <div class="col-6 col-md-6 d-flex align-items-center justify-content-between flex-wrap">
                                <div class="quantity-section">
                                    <div class="d-md-none text-muted small font-weight-bold mb-1">Quantity</div>
                                    <div class="input-group d-flex justify-content-center">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" style="min-width: 30px;max-width: 30px;" data-offline-qty-minus="${item.id}">-</button>
                                        </div>
                                        <input type="number" class="form-control" min="1" value="${item.qty}" style="min-width: 40px;max-width: 50px;" data-offline-qty-input="${item.id}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" style="min-width: 30px;max-width: 30px;" data-offline-qty-plus="${item.id}">+</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="action-section mt-2 mt-md-0 text-md-center">
                                    <a href="#" class="text-danger ms-3" data-offline-remove="${item.id}">
                                        <i class="bi bi-x-circle font-2xl"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            `;
        }

        let summary = document.getElementById('pos-offline-summary');
        if (!summary) {
            summary = document.createElement('div');
            summary.id = 'pos-offline-summary';
            summary.className = 'card-body p-3';
            list.insertAdjacentElement('afterend', summary);
        }

        summary.innerHTML = `
            <div class="d-flex justify-content-between py-1 font-weight-bold text-dark">
                <span>Sub Total</span>
                <span>${money(totals.subtotal)}</span>
            </div>
            <hr class="my-1">
            <div class="d-flex justify-content-between py-2 h5 font-weight-bold">
                <span>Total</span>
                <span>${money(totals.total)}</span>
            </div>
        `;

        const disabled = !items.length || totals.total <= 0;
        document.querySelectorAll('[wire\\:click*="saveOrderPending"], [wire\\:click="proceed"], [wire\\:click*="resetCart"]').forEach((button) => {
            button.disabled = button.getAttribute('wire:click')?.includes('resetCart') ? !items.length : disabled;
        });
    };

    const showOfflineAlert = (message, type = 'info') => {
        const container = document.querySelector('[data-pos-checkout-root]') || document.querySelector('.cart-list');
        if (!container) return;

        const existing = document.getElementById('pos-offline-alert');
        if (existing) existing.remove();

        const alert = document.createElement('div');
        alert.id = 'pos-offline-alert';
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `<div class="alert-body"><span>${escapeHtml(message)}</span></div>`;
        container.prepend(alert);
        setTimeout(() => alert.remove(), 3500);
    };

    const renderSyncStatus = async (message = null, type = 'info') => {
        const root = document.querySelector('[data-pos-checkout-root]') || document.querySelector('.cart-list');
        if (!root) return;

        const orders = await all('orders');
        const pending = orders.filter((order) => order.sync_status !== 'synced');
        let status = document.getElementById('pos-offline-sync-status');

        if (!pending.length && !message) {
            if (status) status.remove();
            return;
        }

        if (!status) {
            status = document.createElement('div');
            status.id = 'pos-offline-sync-status';
            root.prepend(status);
        }

        const failed = pending.filter((order) => order.sync_status === 'failed');
        const text = message || (
            failed.length
                ? `${failed.length} transaksi offline gagal sync. Akan dicoba ulang.`
                : `${pending.length} transaksi offline menunggu sync.`
        );

        status.className = `alert alert-${type} py-2`;
        status.innerHTML = `<small>${escapeHtml(text)}</small>`;
    };

    const renderSelectedTables = async () => {
        const customerGroup = document.getElementById('customer_name')?.closest('.form-group');
        if (!customerGroup) return;

        let holder = document.getElementById('pos-offline-selected-tables');
        if (!holder) {
            holder = document.createElement('div');
            holder.id = 'pos-offline-selected-tables';
            holder.className = 'mt-2';
            customerGroup.appendChild(holder);
        }

        if (!offlineState.selectedTableNames.length) {
            holder.innerHTML = '';
            return;
        }

        holder.innerHTML = `
            <div class="d-flex align-items-center flex-wrap">
                <small class="text-muted mb-1" style="margin-right: 4px;">Table:</small>
                <div class="d-flex flex-wrap">
                    ${offlineState.selectedTableNames.map((name, index) => `
                        <span class="badge bg-primary text-white mb-1 p-1 text-sm" style="margin-right: 2px;">
                            ${escapeHtml(name)}
                            <i class="bi bi-x-circle ms-1" style="font-size: 0.8em; cursor: pointer;" data-offline-remove-table="${index}"></i>
                        </span>
                    `).join('')}
                </div>
            </div>
        `;
    };

    const toggleTable = async (tableId) => {
        const id = Number(tableId);
        const idx = offlineState.selectedTableIds.indexOf(id);
        const table = await get('tables', id);

        if (idx >= 0) {
            offlineState.selectedTableIds.splice(idx, 1);
            offlineState.selectedTableNames.splice(idx, 1);
        } else {
            offlineState.selectedTableIds.push(id);
            offlineState.selectedTableNames.push(table?.name || table?.no_meja || `Meja ${id}`);
        }

        document.querySelectorAll(`#modal-meja-${id}`).forEach((node) => node.classList.toggle('selected', idx < 0));
        await renderSelectedTables();
    };

    const renderPendingOrders = async () => {
        const modal = document.getElementById('pendingOrdersModal');
        if (!modal) return;

        const body = modal.querySelector('.modal-body');
        if (!body) return;

        const serverPending = await all('pendingOrders');
        const localPending = (await all('orders')).filter((order) => order.status === 'Pending' && order.sync_status !== 'synced');
        const rows = [
            ...localPending.map((order) => ({ ...order, reference: order.local_reference, source: 'local' })),
            ...serverPending.map((order) => ({ ...order, source: 'server' })),
        ];

        if (!rows.length) {
            body.innerHTML = '<div class="text-center text-muted py-3">No pending orders found.</div>';
        } else {
            body.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Reference</th>
                                <th>Customer</th>
                                <th>Table</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows.map((order, index) => `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${escapeHtml(order.reference)}</td>
                                    <td>${escapeHtml(order.customer_name || 'Guest')}</td>
                                    <td>${escapeHtml((order.selected_table_ids || []).join(', ') || '-')}</td>
                                    <td>${escapeHtml(order.date || order.created_at || '')}</td>
                                    <td>${money(order.total_amount)}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-secondary mr-1" disabled title="Butuh server aktif">Print KOT</button>
                                        <button type="button" class="btn btn-sm btn-info mr-1" data-offline-order-detail="${escapeHtml(order.reference)}">Detail</button>
                                        <button type="button" class="btn btn-sm btn-warning mr-1" disabled title="Butuh server aktif">Pre-Bill</button>
                                        <button type="button" class="btn btn-sm btn-success" data-offline-order-select="${escapeHtml(order.reference)}">Select</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }

        if (window.jQuery) window.jQuery('#pendingOrdersModal').modal('show');
    };

    const loadPendingOrderToCart = async (reference) => {
        const localOrder = await get('orders', reference);
        const serverOrder = await get('pendingOrders', reference);
        const order = localOrder || serverOrder;
        if (!order) return;

        await clear('cart');
        for (const item of order.items || []) {
            await put('cart', {
                id: Number(item.id),
                name: item.name,
                code: item.code,
                qty: Number(item.qty || 1),
                price: Number(item.price || 0),
                unit_price: Number(item.unit_price || item.price || 0),
                product_tax: Number(item.product_tax || 0),
                product_discount: Number(item.product_discount || 0),
                product_discount_type: item.product_discount_type || 'fixed',
                stock: Number(item.stock || 0),
                unit: item.unit || '',
                variants: item.variants || [],
            });
        }

        const customer = document.getElementById('customer_name');
        if (customer) customer.value = order.customer_name || '';
        offlineState.orderType = order.order_type || 'dine_in';
        offlineState.selectedTableIds = (order.selected_table_ids || []).map(Number);
        offlineState.selectedTableNames = offlineState.selectedTableIds.map((id) => `Meja ${id}`);

        await renderSelectedTables();
        await renderOfflineCart();
        if (window.jQuery) window.jQuery('#pendingOrdersModal').modal('hide');
    };

    const showOfflineOrderDetail = async (reference) => {
        const order = (await get('orders', reference)) || (await get('pendingOrders', reference));
        if (!order) return;

        const detailModal = document.getElementById('orderDetailModal');
        if (!detailModal) return;

        const body = detailModal.querySelector('.modal-body');
        if (!body) return;

        const items = order.items || [];
        body.innerHTML = `
            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr><th>#</th><th>Product</th><th>Qty</th><th>Price</th><th>Sub Total</th></tr>
                    </thead>
                    <tbody>
                        ${items.map((item, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${escapeHtml(item.name)}</td>
                                <td>${item.qty}</td>
                                <td>${money(item.price)}</td>
                                <td>${money(Number(item.price || 0) * Number(item.qty || 0))}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <div class="border-top pt-2" style="width: 300px;">
                    <div class="d-flex justify-content-between font-weight-bold mt-2">
                        <span style="font-size: 1.1rem;">Total</span>
                        <span style="font-size: 1.1rem;" class="text-primary">${money(order.total_amount)}</span>
                    </div>
                </div>
            </div>
        `;

        if (window.jQuery) window.jQuery('#orderDetailModal').modal('show');
    };

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));

    const localReference = () => {
        const date = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        return [
            'OFF',
            date.getFullYear() + pad(date.getMonth() + 1) + pad(date.getDate()),
            pad(date.getHours()) + pad(date.getMinutes()) + pad(date.getSeconds()),
            Math.random().toString(36).slice(2, 7).toUpperCase(),
        ].join('-');
    };

    const readOrderContext = async (status) => {
        const items = await cartItems();
        const totals = calculateTotals(items);
        const paid = Number(document.getElementById('paid_amount')?.value || (status === 'Completed' ? totals.total : 0));
        const paymentIds = ['cash', 'debitcard', 'creditcard', 'gopay', 'ovo', 'shopeepay', 'kredivo', 'dana', 'grabpay', 'qris'];
        const payments = {};

        paymentIds.forEach((id) => {
            payments[id] = Number(document.getElementById(id)?.value || 0);
        });

        return {
            local_reference: localReference(),
            status,
            customer_name: document.getElementById('customer_name')?.value || 'Guest',
            order_type: offlineState.orderType || document.querySelector('input[name="order_type"]')?.value || 'dine_in',
            table_id: document.querySelector('input[name="table_id"]')?.value || null,
            selected_table_ids: offlineState.selectedTableIds.length ? offlineState.selectedTableIds : (readJsonInput('selected_table_ids') || []),
            tax_percentage: 0,
            discount_percentage: 0,
            shipping_amount: 0,
            service_charge: 0,
            lain_a: 0,
            lain_b: 0,
            tax_amount: totals.tax_amount,
            discount_amount: totals.discount_amount,
            total_amount: totals.total,
            paid_amount: paid,
            payments,
            items: items.map((item) => ({
                id: item.id,
                name: item.name || item.product_name,
                code: item.code || item.product_code,
                qty: Number(item.qty || 1),
                price: Number(item.price || item.product_price || 0),
                unit_price: Number(item.unit_price || item.price || item.product_price || 0),
                product_tax: Number(item.product_tax || 0),
                product_discount: Number(item.product_discount || 0),
                product_discount_type: item.product_discount_type || 'fixed',
                variants: item.variants || [],
            })),
            note: 'Created while POS was offline',
            sync_status: 'pending',
            created_at: new Date().toISOString(),
        };
    };

    const readJsonInput = (name) => {
        const input = document.querySelector(`[name="${name}"]`);
        if (!input?.value) return null;
        try {
            return JSON.parse(input.value);
        } catch (e) {
            return null;
        }
    };

    const queueOrder = async (status) => {
        const items = await cartItems();
        if (!items.length) {
            showOfflineAlert('Keranjang masih kosong!', 'warning');
            return;
        }

        const order = await readOrderContext(status);
        await put('orders', order);
        await clear('cart');
        await renderOfflineCart();
        await renderSyncStatus('Transaksi offline tersimpan di browser. Menunggu server hidup untuk sync.', 'warning');
        showOfflineAlert(status === 'Pending' ? 'Order offline disimpan. Akan sync saat online.' : 'Sale offline disimpan. Akan sync saat online.', 'success');

        if (window.jQuery) {
            window.jQuery('#checkoutModal').modal('hide');
        }

        await syncOrders(true);
    };

    const showCheckoutModal = async () => {
        const items = await cartItems();
        const totals = calculateTotals(items);

        if (!items.length) {
            showOfflineAlert('Keranjang masih kosong!', 'warning');
            return;
        }

        const totalInput = document.getElementById('total_amount');
        if (totalInput) totalInput.value = totals.total;

        const labels = [
            ['lblreceipt', 0],
            ['lblkembalian', 0],
        ];

        labels.forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = money(value);
        });

        const totalLabel = document.querySelector('#checkoutModal .table-primary .text-right');
        if (totalLabel) totalLabel.textContent = money(totals.total);

        const tagihan = document.querySelector('#checkoutModal .font-weight-bold');
        if (tagihan) tagihan.textContent = money(totals.total);

        const actionButton = document.getElementById('actionbutton');
        if (actionButton) actionButton.disabled = false;

        if (window.jQuery) {
            window.jQuery('#checkoutModal').modal('show');
        }
    };

    const syncOrders = async (force = false) => {
        if (syncInProgress || !config.syncUrl) return;
        if (!(await checkAppReachability(force))) {
            await renderSyncStatus('Server POS belum bisa dijangkau. Transaksi offline masih tersimpan di browser.', 'warning');
            return;
        }

        const pending = (await all('orders')).filter((order) => order.sync_status !== 'synced');
        if (!pending.length) return;

        syncInProgress = true;
        await renderSyncStatus(`Mencoba sync ${pending.length} transaksi offline...`, 'info');

        try {
            const response = await fetch(config.syncUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ orders: pending }),
            });

            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Sync ditolak server (HTTP ${response.status}). ${text.slice(0, 120)}`);
            }

            const result = await response.json();

            for (const item of result.synced || []) {
                const order = await get('orders', item.local_reference);
                if (order) {
                    order.sync_status = 'synced';
                    order.server_reference = item.reference;
                    order.synced_at = new Date().toISOString();
                    await put('orders', order);
                }
            }

            for (const item of result.failed || []) {
                const order = await get('orders', item.local_reference);
                if (order) {
                    order.sync_status = 'failed';
                    order.sync_error = item.message;
                    order.last_sync_attempt_at = new Date().toISOString();
                    await put('orders', order);
                }
            }

            const remaining = (await all('orders')).filter((order) => order.sync_status !== 'synced');
            if (remaining.length) {
                await renderSyncStatus(`${remaining.length} transaksi offline belum masuk. ${remaining[0].sync_error || 'Akan dicoba ulang.'}`, 'danger');
            } else {
                await renderSyncStatus('Semua transaksi offline berhasil masuk ke server.', 'success');
                setTimeout(() => renderSyncStatus(), 4000);
            }
        } catch (e) {
            const orders = await all('orders');
            for (const order of orders.filter((item) => item.sync_status !== 'synced')) {
                order.sync_status = 'failed';
                order.sync_error = e.message || 'Sync gagal.';
                order.last_sync_attempt_at = new Date().toISOString();
                await put('orders', order);
            }
            await renderSyncStatus(e.message || 'Sync gagal. Akan dicoba ulang.', 'danger');
        } finally {
            syncInProgress = false;
        }
    };

    const bindEvents = () => {
        document.addEventListener('click', async (event) => {
            await checkAppReachability();
            if (!offlineMode()) return;

            const productNode = event.target.closest('[data-offline-product]');
            if (productNode) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                await addProductToCart(JSON.parse(productNode.dataset.offlineProduct));
                return;
            }

            const searchSelect = event.target.closest('[data-offline-search-select]');
            if (searchSelect) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                const product = await get('products', Number(searchSelect.dataset.offlineSearchSelect));
                if (product) await addProductToCart(product);
                document.querySelectorAll('[data-offline-search-results]').forEach((node) => node.remove());
                return;
            }

            const tableNode = event.target.closest('[id^="modal-meja-"]');
            if (tableNode) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                await toggleTable(tableNode.id.replace('modal-meja-', ''));
                return;
            }

            const removeTable = event.target.closest('[data-offline-remove-table]');
            if (removeTable) {
                event.preventDefault();
                const index = Number(removeTable.dataset.offlineRemoveTable);
                offlineState.selectedTableIds.splice(index, 1);
                offlineState.selectedTableNames.splice(index, 1);
                await renderSelectedTables();
                return;
            }

            const orderSelect = event.target.closest('[data-offline-order-select]');
            if (orderSelect) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                await loadPendingOrderToCart(orderSelect.dataset.offlineOrderSelect);
                return;
            }

            const orderDetail = event.target.closest('[data-offline-order-detail]');
            if (orderDetail) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                await showOfflineOrderDetail(orderDetail.dataset.offlineOrderDetail);
                return;
            }

            const plus = event.target.closest('[data-offline-qty-plus]');
            if (plus) {
                event.preventDefault();
                const item = await get('cart', Number(plus.dataset.offlineQtyPlus));
                await setQty(item.id, Number(item.qty) + 1);
                return;
            }

            const minus = event.target.closest('[data-offline-qty-minus]');
            if (minus) {
                event.preventDefault();
                const item = await get('cart', Number(minus.dataset.offlineQtyMinus));
                await setQty(item.id, Number(item.qty) - 1);
                return;
            }

            const remove = event.target.closest('[data-offline-remove]');
            if (remove) {
                event.preventDefault();
                await removeItem(remove.dataset.offlineRemove);
                return;
            }

            const wireButton = event.target.closest('[wire\\:click]');
            const wireAction = wireButton?.getAttribute('wire:click') || '';

            if (wireAction.includes('saveOrderPending')) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                await queueOrder('Pending');
            } else if (wireAction === 'proceed') {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                await showCheckoutModal();
            } else if (wireAction.includes('resetCart')) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                await clear('cart');
                await renderOfflineCart();
            } else if (wireAction.includes('show-pending-orders-modal')) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                await renderPendingOrders();
            } else if (wireAction.includes("$set('order_type'")) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                offlineState.orderType = wireAction.includes('take_out') ? 'take_out' : 'dine_in';
                const orderInput = document.querySelector('input[name="order_type"]');
                if (orderInput) orderInput.value = offlineState.orderType;
                document.querySelectorAll('[wire\\:click]').forEach((button) => {
                    const action = button.getAttribute('wire:click') || '';
                    if (!action.includes("$set('order_type'")) return;
                    const active = action.includes(offlineState.orderType);
                    button.classList.toggle('btn-primary', active);
                    button.classList.toggle('btn-outline-primary', !active);
                });
            }
        }, true);

        document.addEventListener('input', async (event) => {
            await checkAppReachability();
            if (offlineMode() && event.target.matches('input[placeholder="Product name or code...."], input[placeholder="Scanner...."]')) {
                event.stopPropagation();
                event.stopImmediatePropagation();
                await renderSearchResults(event.target);
            }
        }, true);

        document.addEventListener('keydown', async (event) => {
            await checkAppReachability();
            if (!offlineMode()) return;

            if (event.key === 'Enter' && event.target.matches('input[placeholder="Scanner...."], input[placeholder="Product name or code...."]')) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                const results = await findProducts(event.target.value, 1);
                if (results[0]) {
                    await addProductToCart(results[0]);
                    event.target.value = '';
                    document.querySelectorAll('[data-offline-search-results]').forEach((node) => node.remove());
                } else {
                    showOfflineAlert('Produk tidak ditemukan.', 'warning');
                }
            }
        }, true);

        document.addEventListener('change', async (event) => {
            await checkAppReachability();
            if (!offlineMode()) return;
            const input = event.target.closest('[data-offline-qty-input]');
            if (input) await setQty(input.dataset.offlineQtyInput, input.value);
        });

        document.addEventListener('submit', async (event) => {
            await checkAppReachability();
            if (!offlineMode() || event.target.id !== 'checkout-form') return;
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            await queueOrder('Completed');
        }, true);

        window.addEventListener('online', async () => {
            appReachable = true;
            await syncMasterData();
            await syncOrders(true);
        });

        window.addEventListener('offline', async () => {
            appReachable = false;
            await renderOfflineProductList();
            await renderOfflineCart();
        });
    };

    document.addEventListener('DOMContentLoaded', async () => {
        registerServiceWorker();
        await snapshotVisibleProducts();
        await checkAppReachability(true);
        await syncMasterData();
        await syncOrders(true);
        bindEvents();
        await renderSyncStatus();
        if (offlineMode()) {
            await renderOfflineProductList();
            await renderOfflineCart();
        }

        setInterval(async () => {
            const wasOffline = offlineMode();
            await checkAppReachability(true);
            if (wasOffline && !offlineMode()) {
                await syncMasterData();
                await syncOrders(true);
            } else if (offlineMode()) {
                await renderOfflineProductList();
                await renderOfflineCart();
                await renderSyncStatus();
            } else {
                await syncOrders(true);
            }
        }, 5000);
    });
})();
