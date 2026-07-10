<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ $currentOrg->name ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div id="htmx-progress"></div>

<div class="app-wrapper">

    {{-- ── Header / Navbar ─────────────────────────────────────────── --}}
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list fs-4"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <span class="navbar-text badge text-bg-primary-subtle text-primary fw-normal fs-6">
                        {{ $currentOrg->name ?? config('app.name') }}
                    </span>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">

                {{-- Dark mode toggle --}}
                <li class="nav-item">
                    <a class="nav-link" href="#" role="button" id="theme-toggle" title="Toggle theme">
                        <i class="bi bi-moon-stars"></i>
                    </a>
                </li>

                {{-- Fullscreen --}}
                <li class="nav-item d-none d-sm-block">
                    <a class="nav-link" href="#" data-lte-toggle="fullscreen" role="button" title="Fullscreen">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </a>
                </li>

                {{-- Notifications --}}
                <li class="nav-item dropdown">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button">
                        <i class="bi bi-bell"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow" style="min-width:16rem">
                        <span class="dropdown-header">Notifications</span>
                        <div class="dropdown-divider"></div>
                        <p class="text-center text-muted small py-3 mb-0">No new notifications</p>
                    </div>
                </li>

                {{-- Profile --}}
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="dropdown" href="#" role="button">
                        <span class="avatar-circle" style="width:36px;height:36px;font-size:.8rem">
                            {{ auth()->user()->initials }}
                        </span>
                        <span class="d-none d-lg-flex flex-column lh-1 text-start">
                            <span class="fw-semibold small">{{ auth()->user()->name }}</span>
                            <span class="text-muted text-capitalize" style="font-size:.72rem">
                                {{ auth()->user()->role }}
                            </span>
                        </span>
                        <i class="bi bi-chevron-down small d-none d-lg-inline"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow">
                        <span class="dropdown-header text-truncate">{{ auth()->user()->email }}</span>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-2"></i>Profile
                        </a>
                        @if(auth()->user()->isOwner())
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Log Out
                            </button>
                        </form>
                    </div>
                </li>

            </ul>
        </div>
    </nav>

    {{-- ── Sidebar ─────────────────────────────────────────────────── --}}
    <aside class="app-sidebar shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center">
                @if($currentOrg?->logo_path)
                    <img src="{{ Storage::url($currentOrg->logo_path) }}" alt="logo"
                         class="brand-image opacity-75 shadow-sm rounded" style="max-height:33px">
                @else
                    <i class="bi bi-receipt-cutoff brand-image fs-4 ms-2 me-1"></i>
                @endif
                <span class="brand-text fw-semibold text-truncate">
                    {{ $currentOrg->name ?? config('app.name') }}
                </span>
            </a>
        </div>

        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                    <li class="nav-header">GENERAL</li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-house-door"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">BUSINESS</li>
                    <li class="nav-item">
                        <a href="{{ route('clients.index') }}"
                           class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Clients</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('projects.index') }}"
                           class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-kanban"></i>
                            <p>Projects</p>
                        </a>
                    </li>

                    <li class="nav-header">FINANCE</li>
                    <li class="nav-item">
                        <a href="{{ route('documents.index') }}"
                           class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-file-earmark-text"></i>
                            <p>Documents</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('payments.index') }}"
                           class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-cash-coin"></i>
                            <p>Payments</p>
                        </a>
                    </li>

                    @if(auth()->user()->isOwner())
                        <li class="nav-header">SETTINGS</li>
                        <li class="nav-item">
                            <a href="{{ route('templates.index') }}"
                               class="nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-layout-text-window-reverse"></i>
                                <p>Templates</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settings.index') }}"
                               class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-gear"></i>
                                <p>Organisation</p>
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->isSuperAdmin())
                        <li class="nav-header">SUPER ADMIN</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-shield-lock"></i>
                                <p>Admin Panel</p>
                            </a>
                        </li>
                    @endif

                </ul>
            </nav>
        </div>
    </aside>

    {{-- ── Main ────────────────────────────────────────────────────── --}}
    <main class="app-main">

        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h3 class="mb-0">{{ $title ?? 'Dashboard' }}</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i></a>
                            </li>
                            {{ $breadcrumb ?? '' }}
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">

                {{-- Flash + validation --}}
                <div id="flash-holder">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Please fix the following:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>

                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────── --}}
    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Invoicing &amp; Projects</div>
        <strong>&copy; {{ now()->year }} {{ $currentOrg->name ?? config('app.name') }}.</strong>
        All rights reserved.
    </footer>

</div>

{{-- ── Shared HTMX modal host ───────────────────────────────────────── --}}
{{-- Trigger with: hx-get="…" hx-target="#app-modal-content" --}}
<div class="modal fade" id="app-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" id="app-modal-dialog">
        <div class="modal-content" id="app-modal-content"></div>
    </div>
</div>

<script>
    // Theme persistence + toggle
    (function () {
        const root = document.documentElement;
        const saved = localStorage.getItem('theme') || 'light';
        root.setAttribute('data-bs-theme', saved);
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('theme-toggle');
            const icon = btn?.querySelector('i');
            const paint = (t) => { if (icon) icon.className = t === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars'; };
            paint(saved);
            btn?.addEventListener('click', function (e) {
                e.preventDefault();
                const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                root.setAttribute('data-bs-theme', next);
                localStorage.setItem('theme', next);
                paint(next);
            });
        });
    })();
</script>

@stack('scripts')
</body>
</html>
