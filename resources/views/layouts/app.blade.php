<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ $currentOrg->name ?? config('app.name') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&display=swap"
          rel="stylesheet">

    <!-- Vendor CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/scrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">

    <!-- Cuba App CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">

    @stack('styles')
</head>

<body>

<!-- Loader -->
<div class="loader-wrapper">
    <div class="loader-index"><span></span></div>
    <svg>
        <defs></defs>
        <filter id="goo">
            <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
            <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo"></fecolormatrix>
        </filter>
    </svg>
</div>

<!-- Tap to top -->
<div class="tap-top"><i data-feather="chevrons-up"></i></div>

<!-- Page wrapper -->
<div class="page-wrapper compact-wrapper" id="pageWrapper">

    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <div class="page-header">
        <div class="header-wrapper row m-0">

            {{-- Search --}}
            <form class="form-inline search-full col" action="#" method="get">
                <div class="form-group w-100">
                    <div class="Typeahead Typeahead--twitterUsers">
                        <div class="u-posRelative">
                            <input class="demo-input Typeahead-input form-control-plaintext w-100"
                                   type="text" placeholder="Search..." name="q" autofocus>
                            <i class="close-search" data-feather="x"></i>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Logo --}}
            <div class="header-logo-wrapper col-auto p-0">
                <div class="logo-wrapper">
                    <a href="{{ route('dashboard') }}">
                        <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}"
                             alt="{{ $currentOrg->name ?? config('app.name') }}">
                        <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                             alt="{{ $currentOrg->name ?? config('app.name') }}">
                    </a>
                </div>
                <div class="toggle-sidebar">
                    <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
                </div>
            </div>

            {{-- Organisation badge (left header area) --}}
            <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
                <div class="d-flex align-items-center h-100 ps-3">
                        <span class="badge badge-light-primary f-14 fw-normal">
                            {{ $currentOrg->name ?? '' }}
                        </span>
                </div>
            </div>

            {{-- Right nav --}}
            <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
                <ul class="nav-menus">

                    {{-- Dark mode toggle --}}
                    <li>
                        <div class="mode">
                            <svg>
                                <use href="{{ asset('assets/svg/icon-sprite.svg#moon') }}"></use>
                            </svg>
                        </div>
                    </li>

                    {{-- Fullscreen --}}
                    <li class="fullscreen-body">
                            <span>
                                <svg id="maximize-screen">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#full-screen') }}"></use>
                                </svg>
                            </span>
                    </li>

                    {{-- Notifications placeholder --}}
                    <li class="onhover-dropdown">
                        <div class="notification-box">
                            <svg>
                                <use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
                            </svg>
                        </div>
                        <div class="onhover-show-div notification-dropdown">
                            <h6 class="f-18 mb-0 dropdown-title">Notifications</h6>
                            <ul>
                                <li class="text-center py-3 text-muted f-13">No new notifications</li>
                            </ul>
                        </div>
                    </li>

                    {{-- Profile dropdown --}}
                    <li class="profile-nav onhover-dropdown pe-0 py-0">
                        <div class="d-flex profile-media align-items-center gap-2">
                            {{-- Avatar circle --}}
                            <div
                                class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center b-r-10"
                                style="width:38px;height:38px;font-size:14px;font-weight:600;flex-shrink:0;">
                                {{ auth()->user()->initials }}
                            </div>
                            <div class="flex-grow-1">
                                <span>{{ auth()->user()->name }}</span>
                                <p class="mb-0 text-capitalize">
                                    {{ auth()->user()->role }}
                                    <i class="middle fa-solid fa-angle-down"></i>
                                </p>
                            </div>
                        </div>
                        <ul class="profile-dropdown onhover-show-div">
                            @if(auth()->user()->isOwner())
                                <li>
                                    <a href="{{ route('settings.index') }}">
                                        <i data-feather="settings"></i>
                                        <span>Settings</span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="dropdown-item p-0 border-0 bg-transparent text-start w-100">
                                        <i data-feather="log-out"></i>
                                        <span>Log Out</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>

        </div>
    </div>
    {{-- ── End Page Header ─────────────────────────────────────── --}}

    {{-- ── Page Body Wrapper ───────────────────────────────────── --}}
    <div class="page-body-wrapper">

        {{-- ── Sidebar ──────────────────────────────────────────── --}}
        <div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
            <div>
                <div class="logo-wrapper">
                    <a href="{{ route('dashboard') }}">
                        <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="">
                        <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="">
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
                    <div class="left-arrow" id="left-arrow">
                        <i data-feather="arrow-left"></i>
                    </div>

                    <div id="sidebar-menu">
                        <ul class="sidebar-links" id="simple-bar">
                            <li class="back-btn">
                                <a href="{{ route('dashboard') }}">
                                    <img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
                                </a>
                                <div class="mobile-back text-end">
                                    <span>Back</span>
                                    <i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i>
                                </div>
                            </li>

                            {{-- General section --}}
                            <li class="sidebar-main-title">
                                <div><h6>General</h6></div>
                            </li>

                            {{-- Dashboard --}}
                            <li class="sidebar-list {{ request()->routeIs('dashboard') ? 'active' : '' }}">
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

                            {{-- Business section --}}
                            <li class="sidebar-main-title">
                                <div><h6>Business</h6></div>
                            </li>

                            {{-- Clients --}}
                            <li class="sidebar-list {{ request()->routeIs('clients.*') ? 'active' : '' }}">
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

                            {{-- Projects --}}
                            <li class="sidebar-list {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                                <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('projects.*') ? 'active' : '' }}"
                                   href="{{ route('projects.index') }}">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-project') }}"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-project') }}"></use>
                                    </svg>
                                    <span>Projects</span>
                                </a>
                            </li>

                            {{-- Finance section --}}
                            <li class="sidebar-main-title">
                                <div><h6>Finance</h6></div>
                            </li>

                            {{-- Documents --}}
                            <li class="sidebar-list {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                                <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                                   href="{{ route('documents.index') }}">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-form') }}"></use>
                                    </svg>
                                    <span>Documents</span>
                                </a>
                            </li>

                            {{-- Payments --}}
                            <li class="sidebar-list {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                                <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                                   href="{{ route('payments.index') }}">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-ecommerce') }}"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-ecommerce') }}"></use>
                                    </svg>
                                    <span>Payments</span>
                                </a>
                            </li>

                            {{-- Settings section — owner only --}}
                            @if(auth()->user()->isOwner())
                                <li class="sidebar-main-title">
                                    <div><h6>Settings</h6></div>
                                </li>

                                {{-- Templates --}}
                                <li class="sidebar-list {{ request()->routeIs('templates.*') ? 'active' : '' }}">
                                    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('templates.*') ? 'active' : '' }}"
                                       href="{{ route('templates.index') }}">
                                        <svg class="stroke-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-layout') }}"></use>
                                        </svg>
                                        <svg class="fill-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#fill-layout') }}"></use>
                                        </svg>
                                        <span>Templates</span>
                                    </a>
                                </li>

                                {{-- Organisation Settings --}}
                                <li class="sidebar-list {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                                       href="{{ route('settings.index') }}">
                                        <svg class="stroke-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-widget') }}"></use>
                                        </svg>
                                        <svg class="fill-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#fill-widget') }}"></use>
                                        </svg>
                                        <span>Organisation</span>
                                    </a>
                                </li>

                            @endif

                            {{-- Super admin section --}}
                            @if(auth()->user()->isSuperAdmin())
                                <li class="sidebar-main-title">
                                    <div><h6>Super Admin</h6></div>
                                </li>
                                <li class="sidebar-list {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                                       href="{{ route('admin.dashboard') }}">
                                        <svg class="stroke-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-bookmark') }}"></use>
                                        </svg>
                                        <svg class="fill-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#fill-bookmark') }}"></use>
                                        </svg>
                                        <span>Admin Panel</span>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>

                    <div class="right-arrow" id="right-arrow">
                        <i data-feather="arrow-right"></i>
                    </div>
                </nav>
            </div>
        </div>
        {{-- ── End Sidebar ──────────────────────────────────────── --}}

        {{-- ── Page Body ────────────────────────────────────────── --}}
        <div class="page-body">
            <div class="container-fluid">

                {{-- Page title + breadcrumb --}}
                <div class="page-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3>{{ $title ?? 'Dashboard' }}</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">
                                        <svg class="stroke-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                        </svg>
                                    </a>
                                </li>
                                {{ $breadcrumb ?? '' }}
                            </ol>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Flash messages --}}
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i data-feather="check-circle" style="width:16px;height:16px" class="me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i data-feather="alert-circle" style="width:16px;height:16px" class="me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i data-feather="alert-triangle" style="width:16px;height:16px" class="me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            {{-- Main slot --}}
            <div class="container-fluid">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 footer-copyright text-center">
                            <p class="mb-0">
                                &copy; {{ now()->year }} {{ $currentOrg->name ?? config('app.name') }}
                            </p>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
        {{-- ── End Page Body ────────────────────────────────────── --}}

    </div>
    {{-- ── End Page Body Wrapper ───────────────────────────────── --}}

</div>
{{-- ── End Page Wrapper ────────────────────────────────────────── --}}

<!-- jQuery -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<!-- Feather icons -->
<script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
<!-- Scrollbar -->
<script src="{{ asset('assets/js/scrollbar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/scrollbar/custom.js') }}"></script>
<!-- Cuba core -->
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
<script src="{{ asset('assets/js/sidebar-pin.js') }}"></script>
<script src="{{ asset('assets/js/header-slick.js') }}"></script>
<script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
<script src="{{ asset('assets/js/height-equal.js') }}"></script>
<!-- Cuba theme -->
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="{{ asset('assets/js/theme-customizer/customizer.js') }}"></script>

@stack('scripts')

</body>
</html>
