<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 "
    id="sidenav-main">
    {{-- Header Nav --}}
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('vendor.dashboard') }}">
            <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="...">
            <span class="ms-3 font-weight-bold">Pay Sabil <strong>Vendor</strong></span>
        </a>
    </div>

    <hr class="horizontal dark mt-0">

    {{-- Main Nav --}}
    <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            {{-- Dashboard --}}
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">MAIN</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('vendor/dashboard') ? 'active' : '' }}"
                    href="{{ route('vendor.dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- Business Management --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">BUSINESS</h6>
            </li>

            {{-- Profile Management --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('vendor/profile') ? 'active' : '' }}"
                    href="{{ route('vendor.profile') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Profile</span>
                </a>
            </li>

            {{-- Services Management --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('vendor/services*') ? 'active' : '' }}"
                    href="{{ route('vendor.services.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-app text-info text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Services</span>
                </a>
            </li>

            {{-- QR Codes --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('vendor/qrcodes*') ? 'active' : '' }}"
                    href="{{ route('vendor.qrcodes.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-image text-success text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">QR Codes</span>
                </a>
            </li>

            {{-- Transactions --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('vendor/transactions*') ? 'active' : '' }}"
                    href="{{ route('vendor.transactions.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-credit-card text-danger text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Transactions</span>
                </a>
            </li>

            {{-- Reviews & Feedback --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('vendor/feedback*') ? 'active' : '' }}"
                    href="{{ route('vendor.feedback.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-chat-round text-purple text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Reviews & Feedback</span>
                </a>
            </li>

            {{-- Account --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">ACCOUNT</h6>
            </li>

            {{-- Settings --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('vendor/settings*') ? 'active' : '' }}"
                    href="{{ route('vendor.settings') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-settings-gear-65 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Settings</span>
                </a>
            </li>

            {{-- Logout --}}
            <li class="nav-item">
                <a class="nav-link text-danger" href="{{ url('logout') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-user-run text-danger text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Logout</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
