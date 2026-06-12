<button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
    data-class="c-sidebar-show">
    <i class="bi bi-list" style="font-size: 2rem;"></i>
</button>

<button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
    data-class="c-sidebar-lg-show" responsive="true">
    <i class="bi bi-list" style="font-size: 2rem;"></i>
</button>

<ul class="c-header-nav d-md-down-none">
    <li class="c-header-nav-item px-3">
        @if (session()->has('selected_outlet_name'))
            <div class="d-flex align-items-center">
                <i class="bi bi-shop text-primary mr-2" style="font-size: 1.2rem;"></i>
                <div>
                    <small class="text-muted d-block" style="line-height: 1;">Active Outlet:</small>
                    <span class="font-weight-bold text-dark">{{ session('selected_outlet_name') }}</span>
                </div>
            </div>
        @endif
    </li>
</ul>

<ul class="c-header-nav ml-auto mr-4">
    @can('create_pos_sales')
        <li class="c-header-nav-item mr-3">
            <a class="btn btn-primary btn-pill {{ request()->routeIs('app.pos.index') ? 'disabled' : '' }}"
                href="{{ route('app.pos.index') }}">
                <i class="bi bi-cart mr-1"></i> POS System
            </a>
        </li>
    @endcan

    @if (isFeatureEnabled('inv_alert'))
        @can('show_notifications')
            <li class="c-header-nav-item dropdown d-md-down-none mr-2">
                <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bi bi-bell" style="font-size: 20px;"></i>
                    <span class="badge badge-pill badge-danger">
                        @php
                            // 🎯 QUERY BARU: Mengambil stok gabungan per gudang dari product_warehouse
                            $low_quantity_products = \Modules\Product\Entities\Product::select(
                                'products.id',
                                'products.product_code',
                                'products.product_name',
                                'products.product_stock_alert',
                                \DB::raw('SUM(product_warehouse.qty) as total_warehouse_qty'),
                            )
                                ->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
                                ->groupBy(
                                    'products.id',
                                    'products.product_code',
                                    'products.product_name',
                                    'products.product_stock_alert',
                                )
                                // Membandingkan hasil SUM(qty) gudang dengan nilai alert limit di produk
                                ->havingRaw('SUM(product_warehouse.qty) <= products.product_stock_alert')
                                ->get();

                            echo $low_quantity_products->count();
                        @endphp
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg pt-0">
                    <div class="dropdown-header bg-light">
                        <strong>{{ $low_quantity_products->count() }} Notifications</strong>
                    </div>
                    @forelse($low_quantity_products as $product)
                        <a class="dropdown-item" href="{{ route('products.show', $product->id) }}">
                            <i class="bi bi-exclamation-triangle mr-1 text-warning"></i>
                            <strong>[{{ $product->product_code }}]</strong>&nbsp;
                            {{ Str::limit($product->product_name, 20) }}&nbsp;
                            <span class="text-danger">(Stok: {{ number_format($product->total_warehouse_qty, 0) }})</span>
                        </a>
                    @empty
                        <a class="dropdown-item text-center" href="#">
                            <i class="bi bi-check-circle mr-2 text-success"></i> All stocks are safe.
                        </a>
                    @endforelse
                </div>
            </li>
        @endcan
    @endif

    <li class="c-header-nav-item dropdown">
        <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
            aria-expanded="false">
            <div class="c-avatar mr-2">
                <img class="c-avatar rounded-circle" src="{{ auth()->user()->getFirstMediaUrl('avatars') }}"
                    alt="Profile Image">
            </div>
            <div class="d-flex flex-column">
                <span class="font-weight-bold">{{ auth()->user()->name }}</span>
                <span class="font-italic">Online <i class="bi bi-circle-fill text-success"
                        style="font-size: 11px;"></i></span>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right pt-0">
            <div class="dropdown-header bg-light py-2"><strong>Settings</strong></div>

            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="mfe-2 bi bi-person" style="font-size: 1.2rem;"></i> Profile
            </a>

            <a class="dropdown-item" href="{{ route('auth.select-outlet') }}">
                <i class="mfe-2 bi bi-arrow-left-right" style="font-size: 1.2rem;"></i> Switch Outlet
            </a>

            <div class="dropdown-divider"></div>

            <a class="dropdown-item" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mfe-2 bi bi-box-arrow-left" style="font-size: 1.2rem;"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </li>
</ul>
