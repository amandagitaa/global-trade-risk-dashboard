<?php

$content = file_get_contents('C:\laragon\www\global-trade-risk-dashboard\resources\views\layouts\navbar.blade.php');

$newScriptHtml = <<<HTML
{{-- Toast Notification Container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div id="syncSuccessToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i> Synchronization completed successfully.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <div id="syncErrorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <span id="syncErrorMsg">Synchronization failed.</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const syncForm = document.getElementById('navbarSyncForm');
    const syncBtn = document.getElementById('navbarSyncBtn');
    const syncIcon = document.getElementById('navbarSyncIcon');
    const syncText = document.getElementById('navbarSyncText');
    const syncDateEl = document.getElementById('navbar-sync-date');
    const syncTimeEl = document.getElementById('navbar-sync-time');

    if (syncForm) {
        syncForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // UI Loading state
            syncBtn.disabled = true;
            syncIcon.classList.add('spinner-border', 'spinner-border-sm');
            syncIcon.classList.remove('bi-arrow-repeat');
            syncText.textContent = 'Syncing...';
            
            // AbortController for Timeout (e.g. 120 seconds for full sync)
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 120000);

            fetch(syncForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(syncForm),
                signal: controller.signal
            })
            .then(async response => {
                clearTimeout(timeoutId);
                const data = await response.json().catch(() => null);
                
                if (!response.ok) {
                    throw new Error(data?.message || 'Server error occurred.');
                }
                
                return data;
            })
            .then(data => {
                if (data && data.success) {
                    // Update DOM
                    syncDateEl.textContent = data.date;
                    syncTimeEl.textContent = data.time;

                    // Show Toast
                    const toastEl = document.getElementById('syncSuccessToast');
                    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                    toast.show();
                } else {
                    throw new Error(data?.message || 'Unknown error occurred.');
                }
            })
            .catch(error => {
                console.error('Sync Error:', error);
                
                let errMsg = 'Synchronization failed.';
                if (error.name === 'AbortError') {
                    errMsg = 'Synchronization timed out after 2 minutes.';
                } else if (error.message) {
                    errMsg = error.message;
                }
                
                document.getElementById('syncErrorMsg').textContent = errMsg;
                const toastEl = document.getElementById('syncErrorToast');
                const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
                toast.show();
            })
            .finally(() => {
                // Reset UI
                syncBtn.disabled = false;
                syncIcon.classList.remove('spinner-border', 'spinner-border-sm');
                syncIcon.classList.add('bi-arrow-repeat');
                syncText.textContent = 'Sync Data';
            });
        });
    }
});
</script>
@endpush
HTML;

$pattern = '/{{\s*-- Toast Notification Container --\s*}}.*?@endpush/s';
$content = preg_replace($pattern, $newScriptHtml, $content);

file_put_contents('C:\laragon\www\global-trade-risk-dashboard\resources\views\layouts\navbar.blade.php', $content);
echo "Navbar JS updated with timeout and error handling.\n";
