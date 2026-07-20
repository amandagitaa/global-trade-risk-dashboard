<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\layouts\navbar.blade.php';
$content = file_get_contents($file);

// Remove the broken scripts part
$parts = explode("@push('scripts')", $content);
$topbar = $parts[0];

$correctScripts = <<<'EOD'
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
EOD;

file_put_contents($file, $topbar . $correctScripts);
echo "navbar.blade.php scripts fixed.\n";
