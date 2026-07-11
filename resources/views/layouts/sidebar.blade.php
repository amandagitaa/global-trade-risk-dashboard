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

    <a href="#">

        <i class="bi bi-cloud-sun"></i>

        Weather

    </a>

    <a href="#">

        <i class="bi bi-currency-exchange"></i>

        Currency

    </a>

    <a href="#">

        <i class="bi bi-graph-up-arrow"></i>

        Economy

    </a>

    <a href="#">

        <i class="bi bi-newspaper"></i>

        News

    </a>

    <a href="#">

        <i class="bi bi-truck"></i>

        Ports

    </a>

    <div class="menu-title">
        Decision Support
    </div>

    <a href="#">

        <i class="bi bi-exclamation-triangle"></i>

        Risk Analysis

    </a>

    <a href="#">

        <i class="bi bi-lightbulb"></i>

        Recommendation

    </a>

    <div class="menu-title">
        Reports
    </div>

    <a href="#">

        <i class="bi bi-file-earmark-text"></i>

        Reports

    </a>

    <a href="#">

        <i class="bi bi-clock-history"></i>

        Risk History

    </a>

    <div class="menu-title">
        Account
    </div>

    <a href="#">

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

    <a href="#">

        <i class="bi bi-box-arrow-right"></i>

        Logout

    </a>

</div>