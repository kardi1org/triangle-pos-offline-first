<li class="c-sidebar-nav-item {{ request()->routeIs('home') ? 'c-active' : '' }}">
    <a class="c-sidebar-nav-link" href="{{ route('home') }}">
        <i class="c-sidebar-nav-icon bi bi-house" style="line-height: 1;"></i> Home
    </a>
</li>

<li class="c-sidebar-nav-item {{ request()->routeIs('shift.*') ? 'active' : '' }}">
    <a class="c-sidebar-nav-link" href="{{ route('shift.index') }}">
        <i class="c-sidebar-nav-icon bi bi-clock-history" style="line-height: 1;"></i> Shift Management
    </a>
</li>

@can('access_products')
    <li
        class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') ? 'c-show' : '' }}">
        <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
            <i class="c-sidebar-nav-icon bi bi-journal-bookmark" style="line-height: 1;"></i> Products
        </a>
        <ul class="c-sidebar-nav-dropdown-items pl-2">
            @can('access_product_categories')
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('product-categories.*') ? 'c-active' : '' }}"
                        href="{{ route('product-categories.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-collection" style="line-height: 1;"></i> Categories
                    </a>
                </li>
            @endcan
            @can('create_products')
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('products.create') ? 'c-active' : '' }}"
                        href="{{ route('products.create') }}">
                        <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Product
                    </a>
                </li>
            @endcan
            <li class="c-sidebar-nav-item ">
                <a class="c-sidebar-nav-link {{ request()->routeIs('products.index') ? 'c-active' : '' }}"
                    href="{{ route('products.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Products
                </a>
            </li>
            @can('print_barcodes')
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('barcode.print') ? 'c-active' : '' }}"
                        href="{{ route('barcode.print') }}">
                        <i class="c-sidebar-nav-icon bi bi-printer" style="line-height: 1;"></i> Print Barcode
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan

@if (isFeatureEnabled('inv_adjust'))
    @can('access_adjustments')
        <li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('adjustments.*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
                <i class="c-sidebar-nav-icon bi bi-clipboard-check" style="line-height: 1;"></i> Stock Adjustments
            </a>
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                @can('create_adjustments')
                    <li class="c-sidebar-nav-item ">
                        <a class="c-sidebar-nav-link {{ request()->routeIs('adjustments.create') ? 'c-active' : '' }}"
                            href="{{ route('adjustments.create') }}">
                            <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Adjustment
                        </a>
                    </li>
                @endcan
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('adjustments.index') ? 'c-active' : '' }}"
                        href="{{ route('adjustments.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Adjustments
                    </a>
                </li>
            </ul>
        </li>
    @endcan
@endif

@if (isFeatureEnabled('fin_quotation'))
    @can('access_quotations')
        <li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('quotations.*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
                <i class="c-sidebar-nav-icon bi bi-cart-check" style="line-height: 1;"></i> Quotations
            </a>
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                @can('create_adjustments')
                    <li class="c-sidebar-nav-item ">
                        <a class="c-sidebar-nav-link {{ request()->routeIs('quotations.create') ? 'c-active' : '' }}"
                            href="{{ route('quotations.create') }}">
                            <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Quotation
                        </a>
                    </li>
                @endcan
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('quotations.index') ? 'c-active' : '' }}"
                        href="{{ route('quotations.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Quotations
                    </a>
                </li>
            </ul>
        </li>
    @endcan
@endif

@if (isFeatureEnabled('inv_purchase'))
    @can('access_purchases')
        <li
            class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('purchases.*') || request()->routeIs('purchase-payments*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
                <i class="c-sidebar-nav-icon bi bi-bag" style="line-height: 1;"></i> Purchases
            </a>
            @can('create_purchase')
                <ul class="c-sidebar-nav-dropdown-items pl-2">
                    <li class="c-sidebar-nav-item ">
                        <a class="c-sidebar-nav-link {{ request()->routeIs('purchases.create') ? 'c-active' : '' }}"
                            href="{{ route('purchases.create') }}">
                            <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Purchase
                        </a>
                    </li>
                </ul>
            @endcan
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('purchases.index') ? 'c-active' : '' }}"
                        href="{{ route('purchases.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Purchases
                    </a>
                </li>
            </ul>
        </li>
    @endcan
@endif

@if (isFeatureEnabled('inv_purch_ret'))
    @can('access_purchase_returns')
        <li
            class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('purchase-returns.*') || request()->routeIs('purchase-return-payments.*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
                <i class="c-sidebar-nav-icon bi bi-arrow-return-right" style="line-height: 1;"></i> Purchase Returns
            </a>
            @can('create_purchase_returns')
                <ul class="c-sidebar-nav-dropdown-items pl-2">
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{ request()->routeIs('purchase-returns.create') ? 'c-active' : '' }}"
                            href="{{ route('purchase-returns.create') }}">
                            <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Purchase Return
                        </a>
                    </li>
                </ul>
            @endcan
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('purchase-returns.index') ? 'c-active' : '' }}"
                        href="{{ route('purchase-returns.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Purchase Returns
                    </a>
                </li>
            </ul>
        </li>
    @endcan
@endif

@can('access_sales')
    <li
        class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('sales.*') || request()->routeIs('sale-payments*') ? 'c-show' : '' }}">
        <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
            <i class="c-sidebar-nav-icon bi bi-receipt" style="line-height: 1;"></i> Sales
        </a>
        @can('create_sales')
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('sales.create') ? 'c-active' : '' }}"
                        href="{{ route('sales.create') }}">
                        <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Sale
                    </a>
                </li>
            </ul>
        @endcan
        <ul class="c-sidebar-nav-dropdown-items pl-2">
            <li class="c-sidebar-nav-item ">
                <a class="c-sidebar-nav-link {{ request()->routeIs('sales.index') ? 'c-active' : '' }}"
                    href="{{ route('sales.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Sales
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('access_sale_returns')
    <li
        class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('sale-returns.*') || request()->routeIs('sale-return-payments.*') ? 'c-show' : '' }}">
        <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
            <i class="c-sidebar-nav-icon bi bi-arrow-return-left" style="line-height: 1;"></i> Sale Returns
        </a>
        @can('create_sale_returns')
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('sale-returns.create') ? 'c-active' : '' }}"
                        href="{{ route('sale-returns.create') }}">
                        <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Sale Return
                    </a>
                </li>
            </ul>
        @endcan
        <ul class="c-sidebar-nav-dropdown-items pl-2">
            <li class="c-sidebar-nav-item ">
                <a class="c-sidebar-nav-link {{ request()->routeIs('sale-returns.index') ? 'c-active' : '' }}"
                    href="{{ route('sale-returns.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Sale Returns
                </a>
            </li>
        </ul>
    </li>
@endcan

@if (isFeatureEnabled('fin_cash_mgmt'))
    @can('access_expenses')
        <li
            class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('budget.*') || request()->routeIs('budget-categories.*') || request()->routeIs('Inventories.*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
                <i class="c-sidebar-nav-icon bi bi-graph-up" style="line-height: 1;"></i> Budget
            </a>
            <ul class="c-sidebar-nav-dropdown-items pl-2">

                @can('create_expenses')
                    <li class="c-sidebar-nav-item ">
                        <a class="c-sidebar-nav-link {{ request()->routeIs('budget.create') ? 'c-active' : '' }}"
                            href="{{ route('budget.create') }}">
                            <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Budget Cash
                        </a>
                    </li>
                @endcan
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('budget.index') ? 'c-active' : '' }}"
                        href="{{ route('budget.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Budget Cash
                    </a>
                </li>
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('Inventories.create') ? 'c-active' : '' }}"
                        href="{{ route('Inventories.create') }}">
                        <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Inventory
                    </a>
                </li>
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('inventories.index') ? 'c-active' : '' }}"
                        href="{{ route('Inventories.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Inventory
                    </a>
                </li>
            </ul>
        </li>
    @endcan
@endif

@if (isFeatureEnabled('fin_expense'))
    @can('access_expenses')
        <li
            class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
                <i class="c-sidebar-nav-icon bi bi-wallet2" style="line-height: 1;"></i> Expenses
            </a>
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                @can('access_expense_categories')
                    <li class="c-sidebar-nav-item  ">
                        <a class="c-sidebar-nav-link {{ request()->routeIs('expense-categories.*') ? 'c-active' : '' }}"
                            href="{{ route('expense-categories.index') }}">
                            <i class="c-sidebar-nav-icon bi bi-collection" style="line-height: 1;"></i> Categories
                        </a>
                    </li>
                @endcan
                @can('create_expenses')
                    <li class="c-sidebar-nav-item  ">
                        <a class="c-sidebar-nav-link {{ request()->routeIs('expenses.create') ? 'c-active' : '' }}"
                            href="{{ route('expenses.create') }}">
                            <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Expense
                        </a>
                    </li>
                @endcan
                <li class="c-sidebar-nav-item ">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('expenses.index') ? 'c-active' : '' }}"
                        href="{{ route('expenses.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> All Expenses
                    </a>
                </li>
            </ul>
        </li>
    @endcan
@endif

@can('access_customers')
    <li
        class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('customers.*') || request()->routeIs('suppliers.*') ? 'c-show' : '' }}">
        <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
            <i class="c-sidebar-nav-icon bi bi-people" style="line-height: 1;"></i> Parties
        </a>
        <ul class="c-sidebar-nav-dropdown-items pl-2 pl-2">
            @can('access_customers')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('customers.*') ? 'c-active' : '' }}"
                        href="{{ route('customers.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-people-fill" style="line-height: 1;"></i> Customers
                    </a>
                </li>
            @endcan
            @can('access_suppliers')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('suppliers.*') ? 'c-active' : '' }}"
                        href="{{ route('suppliers.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-people-fill" style="line-height: 1;"></i> Suppliers
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan

@can('access_reports')
    <li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('*-report.index') ? 'c-show' : '' }}">
        <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
            <i class="c-sidebar-nav-icon bi bi-graph-up" style="line-height: 1;"></i> Reports
        </a>
        <ul class="c-sidebar-nav-dropdown-items pl-2">
            @if (isFeatureEnabled('rep_profit_loss'))
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('profit-loss-report.index') ? 'c-active' : '' }}"
                        href="{{ route('profit-loss-report.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-clipboard-data" style="line-height: 1;"></i> Profit / Loss
                        Report
                    </a>
                </li>
            @endif
            @if (isFeatureEnabled('rep_payment'))
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('payments-report.index') ? 'c-active' : '' }}"
                        href="{{ route('payments-report.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-clipboard-data" style="line-height: 1;"></i> Payments Report
                    </a>
                </li>
            @endif
            @if (isFeatureEnabled('rep_sales'))
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('sales-report.index') ? 'c-active' : '' }}"
                        href="{{ route('sales-report.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-clipboard-data" style="line-height: 1;"></i> Sales Report
                    </a>
                </li>
            @endif
            @if (isFeatureEnabled('rep_purchases'))
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('purchases-report.index') ? 'c-active' : '' }}"
                        href="{{ route('purchases-report.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-clipboard-data" style="line-height: 1;"></i> Purchases Report
                    </a>
                </li>
            @endif
            @if (isFeatureEnabled('rep_sales_ret'))
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('sales-return-report.index') ? 'c-active' : '' }}"
                        href="{{ route('sales-return-report.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-clipboard-data" style="line-height: 1;"></i> Sales Return
                        Report
                    </a>
                </li>
            @endif
            @if (isFeatureEnabled('rep_purch_ret'))
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('purchases-return-report.index') ? 'c-active' : '' }}"
                        href="{{ route('purchases-return-report.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-clipboard-data" style="line-height: 1;"></i> Purchases Return
                        Report
                    </a>
                </li>
            @endif
            @if (isFeatureEnabled('rep_shift'))
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('shift.reports') ? 'c-active' : '' }}"
                        href="{{ route('shift.reports') }}">
                        <i class="c-sidebar-nav-icon bi bi-file-earmark-bar-graph" style="line-height: 1;"></i> Shift
                        History
                        Report
                    </a>
                </li>
            @endif
        </ul>
    </li>
@endcan

@can('access_user_management')
    <li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('roles*') ? 'c-show' : '' }}">
        <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
            <i class="c-sidebar-nav-icon bi bi-people" style="line-height: 1;"></i> User Management
        </a>
        <ul class="c-sidebar-nav-dropdown-items pl-2">
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('users.create') ? 'c-active' : '' }}"
                    href="{{ route('users.create') }}">
                    <i class="c-sidebar-nav-icon bi bi-person-plus" style="line-height: 1;"></i> Create User
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('users*') ? 'c-active' : '' }}"
                    href="{{ route('users.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-person-lines-fill" style="line-height: 1;"></i> All Users
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('roles*') ? 'c-active' : '' }}"
                    href="{{ route('roles.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-key" style="line-height: 1;"></i> Roles & Permissions
                </a>
            </li>
        </ul>
    </li>
@endcan

@if (auth()->user()->hasRole('Super Admin'))
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link {{ request()->routeIs('feature-manager.*') ? 'c-active' : '' }}"
            href="{{ route('feature-manager.index') }}">
            <i class="c-sidebar-nav-icon bi bi-shield-lock" style="line-height: 1;"></i> Feature Manager
        </a>
    </li>
@endif

@can('access_settings')
    <li
        class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('currencies*') || request()->routeIs('units*') || request()->routeIs('service-charge*') || request()->routeIs('order-summary*') ? 'c-show' : '' }}">
        <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
            <i class="c-sidebar-nav-icon bi bi-gear" style="line-height: 1;"></i> Settings
        </a>

        @can('access_currencies')
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('mejas*') ? 'c-active' : '' }}"
                        href="{{ route('mejas.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-receipt" style="line-height: 1;"></i> Table
                    </a>
                </li>
            </ul>
        @endcan

        {{-- ✅ Menu Baru: Order Summary Settings --}}
        @can('access_settings')
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('order-summary*') ? 'c-active' : '' }}"
                        href="{{ route('order-summary.index') }}">
                        {{-- Ganti icon ke bi-list-check agar pasti muncul --}}
                        <i class="c-sidebar-nav-icon bi bi-list-check" style="line-height: 1;"></i> Order Summary
                    </a>
                </li>
            </ul>
        @endcan

        @can('access_settings')
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('service-charge*') ? 'c-active' : '' }}"
                        href="{{ route('service-charge.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-percent" style="line-height: 1;"></i> Service Charge
                    </a>
                </li>
            </ul>
        @endcan

        @can('access_currencies')
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('units*') ? 'c-active' : '' }}"
                        href="{{ route('units.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-calculator" style="line-height: 1;"></i> Units
                    </a>
                </li>
            </ul>
        @endcan

        @can('access_currencies')
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('currencies*') ? 'c-active' : '' }}"
                        href="{{ route('currencies.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-cash-stack" style="line-height: 1;"></i> Currencies
                    </a>
                </li>
            </ul>
        @endcan

        @can('access_currencies')
            <ul class="c-sidebar-nav-dropdown-items pl-2">
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->routeIs('payment*') ? 'c-active' : '' }}"
                        href="{{ route('payment.index') }}">
                        <i class="c-sidebar-nav-icon bi bi-wallet2" style="line-height: 1;"></i> Receive Methode
                    </a>
                </li>
            </ul>
        @endcan

        @if (isFeatureEnabled('set_system'))
            @can('access_settings')
                <ul class="c-sidebar-nav-dropdown-items pl-2">
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{ request()->routeIs('settings*') ? 'c-active' : '' }}"
                            href="{{ route('settings.index') }}">
                            <i class="c-sidebar-nav-icon bi bi-sliders" style="line-height: 1;"></i> System Settings
                        </a>
                    </li>
                </ul>
            @endcan
        @endif
    </li>
@endcan
