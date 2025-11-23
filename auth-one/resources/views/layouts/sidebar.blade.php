<aside class="main-sidebar sidebar-dark-primary elevation-4">
    {{-- Brand Logo --}}
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <img src="{{ asset('admin/dist/img/AdminLTELogo.png') }}" alt="SCM Logo"
             class="brand-image img-circle elevation-3" style="opacity:.9; background:white;">
        <span class="brand-text font-weight-bold text-uppercase">SCM Panel</span>
    </a>

    {{-- Sidebar --}}
    <div class="sidebar">
        {{-- User Info --}}
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center border-bottom">
            <div class="image">
                <img src="{{ asset('admin/dist/img/photo3.jpg') }}" class="img-circle elevation-2" alt="User">
            </div>
            <div class="info">
                <a href="#" class="d-block fw-semibold text-white">
                    {{ Auth::user()->name ?? 'Guest' }}
                    <small class="d-block text-muted" style="font-size: 12px;">
                        {{ ucfirst(Auth::user()->getPrimaryRole() ?? 'Guest') }}
                    </small>
                </a>
            </div>
        </div>

        {{-- Sidebar Menu --}}
        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                {{-- Dashboard --}}
                <li class="nav-item mb-1">
                    <a href="{{ route(Auth::user()->getPrimaryRole() . '.dashboard') }}"
                       class="nav-link {{ request()->routeIs(Auth::user()->getPrimaryRole() . '.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- ================= Superadmin Section ================= --}}
                @if (Auth::user()->hasRole('superadmin'))
                    {{-- Administration/User Management --}}
                    <li class="nav-item mb-1 mt-3">
                        <a href="{{ route('superadmin.users.index') }}"
                           class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>User Management</p>
                        </a>
                    </li>

                    {{-- Raw Material Section (Treeview) --}}
                    <?php
                        $rawMaterialRoutes = [
                            'superadmin.raw-materials.*',
                            'superadmin.raw-material-purchases.*',
                            'superadmin.raw-material-stock-out.*',
                            'superadmin.raw-material-stock.index',
                            'superadmin.wastage.*'
                        ];
                        $isRawMaterialActive = in_array(true, array_map(fn($route) => request()->routeIs($route), $rawMaterialRoutes));
                    ?>

                    <li class="nav-item {{ $isRawMaterialActive ? 'menu-open' : '' }} mt-3">
                        <a href="#" class="nav-link {{ $isRawMaterialActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Raw Material <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- Material List Sub-Menu --}}
                            <li class="nav-item">
                                <a href="{{ route('superadmin.raw-materials.index') }}"
                                   class="nav-link {{ request()->routeIs('superadmin.raw-materials.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Material List</p>
                                </a>
                            </li>
                            {{-- Stock In Sub-menus --}}
                            <li class="nav-item">
                                <a href="{{ route('superadmin.raw-material-purchases.create') }}" class="nav-link {{ request()->routeIs('superadmin.raw-material-purchases.create') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Stock In (Purchase)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('superadmin.raw-material-purchases.index') }}" class="nav-link {{ request()->routeIs('superadmin.raw-material-purchases.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Stock In Invoice</p>
                                </a>
                            </li>
                            {{-- Stock Out Sub-menus --}}
                            <li class="nav-item">
                                <a href="{{ route('superadmin.raw-material-stock-out.create') }}" class="nav-link {{ request()->routeIs('superadmin.raw-material-stock-out.create') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Stock Out (Issue)</p>
                                </a>
                            </li>
                            {{-- Stock Report & Wastage --}}
                            <li class="nav-item">
                                <a href="{{ route('superadmin.raw-material-stock.index') }}" class="nav-link {{ request()->routeIs('superadmin.raw-material-stock.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Stock Report</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('superadmin.wastage.index') }}" class="nav-link {{ request()->routeIs('superadmin.wastage.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Wastage Entry/List</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- PRODUCT MANAGEMENT (UPDATED BLOCK WITH SALES) --}}
                    <?php
                        $productRoutes = [
                            'superadmin.products.*',
                            'superadmin.product-receives.*',
                            'superadmin.sales.*',
                            'superadmin.product-returns.*',
                            'superadmin.product-wastage.*'
                        ];
                        $isProductActive = in_array(true, array_map(fn($route) => request()->routeIs($route), $productRoutes));
                    ?>
                    <li class="nav-item {{ $isProductActive ? 'menu-open' : '' }} mt-3">
                        <a href="#" class="nav-link {{ $isProductActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cube"></i>
                            <p>Product/Finished Goods <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- 1. Product List (Product Entry) --}}
                            <li class="nav-item">
                                <a href="{{ route('superadmin.products.index') }}" class="nav-link {{ request()->routeIs('superadmin.products.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Product List / Entry</p>
                                </a>
                            </li>
                            {{-- 2. Product Receive (In) --}}
                            <li class="nav-item">
                                <a href="{{ route('superadmin.product-receives.index') }}" class="nav-link {{ request()->routeIs('superadmin.product-receives.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Product Receive (In)</p>
                                </a>
                            </li>

                            {{-- 3. SALES MANAGEMENT --}}
                            <li class="nav-item has-treeview {{ request()->routeIs('superadmin.sales.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs('superadmin.sales.*') ? 'active' : '' }}">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>
                                        Sales to Depo (Out)
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('superadmin.sales.index') }}" class="nav-link {{ request()->routeIs('superadmin.sales.index') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Sales Invoice List</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('superadmin.sales.create') }}" class="nav-link {{ request()->routeIs('superadmin.sales.create') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Create New Invoice</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            {{-- 4. Product Return --}}
                            <li class="nav-item">
                                <a href="#" class="nav-link {{ request()->routeIs('superadmin.product-returns.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Product Return List</p>
                                </a>
                            </li>
                            {{-- 5. Product Wastage --}}
                            <li class="nav-item">
                                <a href="#" class="nav-link {{ request()->routeIs('superadmin.product-wastage.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Wastage List / Entry</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Settings Section (Treeview) --}}
                    <?php
                        $settingsRoutes = ['superadmin.suppliers.*', 'superadmin.depo.index', 'superadmin.distributor.*'];
                        $isSettingsActive = in_array(true, array_map(fn($route) => request()->routeIs($route), $settingsRoutes));
                    ?>

                    <li class="nav-item {{ $isSettingsActive ? 'menu-open' : '' }} mt-3">
                        <a href="#" class="nav-link {{ $isSettingsActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Settings<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('superadmin.suppliers.index') }}"
                                   class="nav-link {{ request()->routeIs('superadmin.suppliers.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Supplier Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('superadmin.depo.index') }}" class="nav-link {{ request()->routeIs('superadmin.depo.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Depo List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('superadmin.distributor.index') }}" class="nav-link {{ request()->routeIs('superadmin.distributor.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Distributor List</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- ================= Depo Section ================= --}}
                @if (Auth::user()->hasRole('depo'))
                    {{-- Sales Approval Menu --}}
                    <?php
                        $invoiceRoutes = ['depo.invoices.*'];
                        $isInvoiceActive = in_array(true, array_map(fn($route) => request()->routeIs($route), $invoiceRoutes));
                    ?>
                    <li class="nav-item {{ $isInvoiceActive ? 'menu-open' : '' }} mt-3">
                        <a href="#" class="nav-link {{ $isInvoiceActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>Warehouse Invoices <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('depo.invoices.pending') }}" class="nav-link {{ request()->routeIs('depo.invoices.pending') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon text-warning"></i>
                                    <p>Pending for Approval</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('depo.invoices.index') }}" class="nav-link {{ request()->routeIs('depo.invoices.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>All Invoices</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item mb-1 mt-3">
                        <a href="{{ route('depo.users.index') }}"
                           class="nav-link {{ request()->routeIs('depo.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Distributor Management</p>
                        </a>
                    </li>
                @endif

                {{-- ================= Distributor Section ================= --}}
                @if (Auth::user()->hasRole('distributor'))
                    <li class="nav-item mb-1 mt-3">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>Customer List</p>
                        </a>
                    </li>
                @endif

                {{-- ================= Logout ================= --}}
                <li class="nav-item mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="nav-link text-danger"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>

{{-- Optional CSS tweaks for clean look --}}
<style>
    .nav-sidebar>.nav-item>.nav-link.active {
        background-color: #1e3a8a !important;
        color: #fff !important;
    }
    .nav-sidebar>.nav-item>.nav-link:hover {
        background-color: #334155 !important;
        color: #fff !important;
    }
</style>