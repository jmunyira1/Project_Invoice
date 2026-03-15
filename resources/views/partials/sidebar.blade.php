<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
    <div>

        <div class="logo-wrapper">
            <a href="{{ route('dashboard') }}">
                <img class="img-fluid for-light"
                     src="{{ asset('assets/images/logo/logo.png') }}"
                     alt="{{ config('app.name') }}">
                <img class="img-fluid for-dark"
                     src="{{ asset('assets/images/logo/logo_dark.png') }}"
                     alt="{{ config('app.name') }}">
            </a>
            <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
            <div class="toggle-sidebar">
                <i class="status_toggle middle sidebar-toggle" data-feather="grid"></i>
            </div>
        </div>

        <div class="logo-icon-wrapper">
            <a href="{{ route('dashboard') }}">
                <img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
            </a>
        </div>

        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>

            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">

                    <li class="back-btn">
                        <a href="{{ route('dashboard') }}">
                            <img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
                        </a>
                        <div class="mobile-back text-end">
                            <span>Back</span>
                            <i class="fa-solid fa-angle-right ps-2"></i>
                        </div>
                    </li>

                    {{-- ── MAIN ── --}}
                    <li class="sidebar-main-title">
                        <div><h6>Main</h6></div>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- ── BUSINESS ── --}}
                    <li class="sidebar-main-title">
                        <div><h6>Business</h6></div>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('clients.*') ? 'active' : '' }}"
                           href="{{ route('clients.index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
                            </svg>
                            <span>Clients</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('jobs.*') ? 'active' : '' }}"
                           href="">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-project') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-project') }}"></use>
                            </svg>
                            <span>Jobs</span>
                        </a>
                    </li>

                    {{-- ── FINANCE ── --}}
                    <li class="sidebar-main-title">
                        <div><h6>Finance</h6></div>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('invoices.*') ? 'active' : '' }}"
                           href="">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-ecommerce') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-ecommerce') }}"></use>
                            </svg>
                            <span>Invoices</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                           href="">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-form') }}"></use>
                            </svg>
                            <span>Payments</span>
                        </a>
                    </li>

                    {{-- ── SETTINGS (owner only) ── --}}
                    @if(auth()->user()->role === 'owner')
                        <li class="sidebar-main-title">
                            <div><h6>Settings</h6></div>
                        </li>

                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                               href="">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-widget') }}"></use>
                                </svg>
                                <svg class="fill-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#fill-widget') }}"></use>
                                </svg>
                                <span>Organisation</span>
                            </a>
                        </li>

                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('team.*') ? 'active' : '' }}"
                               href="">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-contact') }}"></use>
                                </svg>
                                <svg class="fill-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#fill-contact') }}"></use>
                                </svg>
                                <span>Team</span>
                            </a>
                        </li>

                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('templates.*') ? 'active' : '' }}"
                               href="{{ route('templates.index') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-email-temp') }}"></use>
                                </svg>
                                <svg class="fill-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#fill-email-temp') }}"></use>
                                </svg>
                                <span>Templates</span>
                            </a>
                        </li>
                    @endif

                    {{-- ── SUPER ADMIN ── --}}
                    @if(auth()->user()->is_super_admin)
                        <li class="sidebar-main-title">
                            <div><h6>Super Admin</h6></div>
                        </li>

                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                               href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-layout') }}"></use>
                                </svg>
                                <svg class="fill-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#fill-layout') }}"></use>
                                </svg>
                                <span>Admin panel</span>
                            </a>
                        </li>
                    @endif

                </ul>
            </div>

            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
