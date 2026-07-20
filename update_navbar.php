<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\layouts\navbar.blade.php';
$content = file_get_contents($file);

// Extract scripts part
$parts = explode('@push(\'scripts\')', $content);
$scripts = count($parts) > 1 ? "@push('scripts')" . $parts[1] : '';

// New layout for navbar topbar
$newTopbar = <<<'EOD'
<div class="topbar d-flex justify-content-between align-items-center">

    {{-- Left: Search --}}
    <div class="d-flex align-items-center flex-grow-1">
        <div class="position-relative" style="max-width:330px; width:100%;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input id="country-search" type="text" class="form-control border-start-0" placeholder="Search country..." autocomplete="off">
            </div>
            <div id="country-result" class="list-group position-absolute shadow w-100" style="top:100%; left:0; z-index:9999; display:none;"></div>
        </div>
    </div>

    {{-- Right: Profile --}}
    <div class="d-flex align-items-center">
        <a href="{{ route('profile') }}" class="text-decoration-none d-flex align-items-center gap-2">
            <div class="text-end">
                <div class="fw-bold text-dark small">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">Administrator</div>
            </div>
            @if(auth()->check() && auth()->user()->avatar)
                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
            @else
                <div class="rounded-circle bg-soft-orange text-orange d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-person-fill"></i>
                </div>
            @endif
        </a>
    </div>

</div>

EOD;

file_put_contents($file, $newTopbar . $scripts);
echo "navbar.blade.php layout updated.\n";
