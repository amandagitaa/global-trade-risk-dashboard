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
                <div class="text-muted" style="font-size: 0.75rem;">Trade Analyst</div>
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('country-search');
    const resultBox = document.getElementById('country-result');
    let debounceTimer;

    // Only apply dashboard fetch logic if we are actually on the dashboard
    const isDashboard = window.location.pathname === '{{ route("dashboard", [], false) }}' || window.location.pathname === '/dashboard';

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();

            if (keyword.length === 0) {
                resultBox.style.display = 'none';
                if (isDashboard && typeof window.fetchDashboard === 'function') {
                    window.fetchDashboard(null); // Reset dashboard
                }
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('countries.search') }}?keyword=` + encodeURIComponent(keyword), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    resultBox.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(country => {
                            const a = document.createElement('a');
                            a.href = 'javascript:void(0)';
                            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                            a.innerHTML = `<span>${country.country_name}</span> <small class="text-muted">${country.country_code}</small>`;
                            
                            a.addEventListener('click', function(e) {
                                e.preventDefault();
                                searchInput.value = country.country_name;
                                resultBox.style.display = 'none';
                                
                                if (isDashboard && typeof window.fetchDashboard === 'function') {
                                    window.fetchDashboard(country.id);
                                } else {
                                    // If not on dashboard, redirect to dashboard with query
                                    window.location.href = '{{ route("dashboard") }}?search_country=' + country.id;
                                }
                            });
                            
                            resultBox.appendChild(a);
                        });
                    } else {
                        resultBox.innerHTML = '<div class="list-group-item text-muted">No country found.</div>';
                    }
                    resultBox.style.display = 'block';
                })
                .catch(err => console.error("Search error:", err));
            }, 400); // 400ms debounce
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultBox.contains(e.target)) {
                resultBox.style.display = 'none';
            }
        });
        
        // Handle search reset when clearing input via backspace
        searchInput.addEventListener('change', function() {
            if(this.value.trim() === '' && isDashboard && typeof window.fetchDashboard === 'function') {
                window.fetchDashboard(null);
            }
        });
    }
});
</script>
@endpush