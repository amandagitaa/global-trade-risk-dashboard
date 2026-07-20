<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Admin Panel - Global Trade Risk')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body{
            background:#F8F9FA;
            font-family: "Segoe UI", sans-serif;
        }

        /* Sidebar */
        .sidebar{
            width:260px;
            position:fixed;
            top:0;
            left:0;
            height:100vh;
            background:#FFFFFF; /* Light theme for Admin */
            border-right:1px solid #E5E7EB;
            padding:25px;
            overflow-y:auto;
            z-index:1000;
        }
        .logo{
            font-size:23px;
            font-weight:bold;
            color:#F97316;
            margin-bottom:35px;
        }
        .menu-title{
            font-size:11px;
            color:#6B7280;
            margin-top:18px;
            margin-bottom:10px;
            text-transform:uppercase;
            letter-spacing:1px;
        }
        .sidebar a{
            display:flex;
            align-items:center;
            gap:10px;
            text-decoration:none;
            color:#374151;
            padding:12px 15px;
            border-radius:12px;
            margin-bottom:6px;
            transition:.25s;
        }
        .sidebar a:hover{
            background:#FFF3E0;
            color:#FF7A00;
        }
        .sidebar .active{
            background:#FFF3E0;
            color:#FF7A00;
            font-weight: 600;
        }

        /* Main Content */
        .main{
            margin-left:260px;
            min-height:100vh;
        }

        /* Navbar */
        .topbar{
            background:white;
            padding:18px 30px;
            border-bottom:1px solid #ececec;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .page-title{
            font-size:22px;
            font-weight:700;
            color:#1f2937;
        }

        /* Panel */
        .panel{
            background:white;
            border:none;
            border-radius:18px;
            box-shadow:0 5px 15px rgba(0,0,0,.05);
            padding:22px;
            margin-bottom:25px;
        }
    </style>
</head>
<body>

{{-- Sidebar Admin --}}
<div class="sidebar">
    <div class="logo">
        <i class="bi bi-shield-lock-fill"></i> Admin Panel
    </div>

    <div class="menu-title">Main</div>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2"></i> Dashboard
    </a>
    
    <div class="menu-title">Management</div>
    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
        <i class="bi bi-people"></i> User Management
    </a>
    <a href="{{ route('admin.countries') }}" class="{{ request()->routeIs('admin.countries') ? 'active' : '' }}">
        <i class="bi bi-globe"></i> Countries Management
    </a>
    <a href="{{ route('admin.ports') }}" class="{{ request()->routeIs('admin.ports') ? 'active' : '' }}">
        <i class="bi bi-geo-alt"></i> Ports Management
    </a>
    <a href="{{ route('admin.news') }}" class="{{ request()->routeIs('admin.news') ? 'active' : '' }}">
        <i class="bi bi-newspaper"></i> News Management
    </a>
    <a href="{{ route('admin.articles') }}" class="{{ request()->routeIs('admin.articles') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i> Articles Management
    </a>

    <div class="menu-title">Configuration</div>
    @if(Route::has('admin.risk-configuration'))
    <a href="{{ route('admin.risk-configuration') }}" class="{{ request()->routeIs('admin.risk-configuration') ? 'active' : '' }}">
        <i class="bi bi-sliders"></i> Risk Configuration
    </a>
    @endif
    
    @if(Route::has('admin.sentiment-dictionary'))
    <a href="{{ route('admin.sentiment-dictionary') }}" class="{{ request()->routeIs('admin.sentiment-dictionary') ? 'active' : '' }}">
        <i class="bi bi-book"></i> Sentiment Dictionary
    </a>
    @endif

    <div class="menu-title">Account</div>
    @if(Route::has('settings.index'))
    <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.index') ? 'active' : '' }}">
        <i class="bi bi-gear"></i> Settings
    </a>
    @endif
    
    @if(Route::has('admin.profile'))
    <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
        <i class="bi bi-person"></i> Profile
    </a>
    @endif
    
    <form method="POST" action="{{ route('logout') }}" class="d-grid mt-4">
        @csrf
        <button type="submit" class="btn btn-outline-danger border-0 text-start ps-3" style="border-radius:12px;">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</div>

<div class="main">
    {{-- Navbar Admin --}}
    <div class="topbar">
        <div class="page-title">@yield('page_title', 'Dashboard')</div>
        <div class="user-info text-muted">
            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }} (Admin)
        </div>
    </div>

    <div class="container-fluid p-4">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
