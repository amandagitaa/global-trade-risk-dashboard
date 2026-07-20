<div class="sidebar">

    {{-- Logo --}}
    <div class="logo">

        <i class="bi bi-globe-americas"></i>

        Global Trade

    </div>

    {{-- Main Menu --}}
    <div class="menu-title">
        Main Menu
    </div>

    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

        <i class="bi bi-speedometer2"></i>

        Dashboard

    </a>

        <a href="{{ route('countries.index') }}"
       class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">

        <i class="bi bi-globe2"></i>

        Countries

    </a>

    <a href="{{ route('compare.index') }}"
       class="{{ request()->routeIs('compare.*') ? 'active' : '' }}">

        <i class="bi bi-arrow-left-right"></i>

        Compare

    </a>

    <a href="{{ route('weather.index') }}"
        class="sidebar-link {{ request()->routeIs('weather.*') ? 'active' : '' }}">
        <i class="bi bi-cloud-sun"></i>
            <span>Weather</span>
    </a>

    <a href="{{ route('currency.index') }}"
    class="nav-link {{ request()->routeIs('currency.*') ? 'active' : '' }}">

        <i class="bi bi-currency-exchange"></i>

        <span>Currency</span>

    </a>

    <a href="{{ route('economy.index') }}"
    class="sidebar-link {{ request()->routeIs('economy.*') ? 'active' : '' }}">

        <i class="bi bi-graph-up-arrow"></i>

        <span>Economy</span>

    </a>

    <a href="{{ route('news.index') }}"
    class="sidebar-link {{ request()->routeIs('news.*') ? 'active' : '' }}">

        <i class="bi bi-newspaper"></i>

        <span>News</span>

    </a>

    <a href="{{ route('ports.index') }}"
    class="sidebar-link {{ request()->routeIs('ports.*') ? 'active' : '' }}">

        <i class="bi bi-geo-alt"></i>

        <span>Ports</span>

    </a>

    <a href="{{ route('trade-planner.index') }}"
    class="sidebar-link {{ request()->routeIs('trade-planner.*') ? 'active' : '' }}">

        <i class="bi bi-water" style="font-size: 20px; width: 24px; text-align: center;"></i>

        <span style="margin-left: 2px;">Trade Planner</span>

    </a>

    <div class="menu-title">
        Decision Support
    </div>

    <a href="{{ route('risk-analysis.index') }}"
    class="{{ request()->routeIs('risk-analysis.*') ? 'active' : '' }}">

        <i class="bi bi-exclamation-triangle"></i>

        Risk Analysis

    </a>

    <a href="{{ route('watch-list.index') }}"
    class="sidebar-link {{ request()->routeIs('watch-list.*') ? 'active' : '' }}">

        <i class="bi bi-star"></i>

        <span>Watch List</span>

    </a>

    <div class="menu-title">
        Reports
    </div>

    <a href="{{ route('reports.index') }}"
        class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i>
        <span>Reports</span>
    </a>

    <div class="menu-title">
        Account
    </div>

    <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">

        <i class="bi bi-person-circle"></i>

        Profile

    </a>

    <hr>

    <div class="small text-muted">

        <strong>System Status</strong>

        <br>

        <span class="text-success">

            ● Online

        </span>

    </div>

    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

        <i class="bi bi-box-arrow-right"></i>

        Logout

    </a>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

</div>