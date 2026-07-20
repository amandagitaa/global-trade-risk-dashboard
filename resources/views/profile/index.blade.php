@extends('layouts.app')

@section('title', 'Profile - Global Trade Intelligence')

@section('content')
<div class="container-fluid mb-5">

    {{-- ==========================================
        HEADER
    ========================================== --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark enterprise-title">Profile Settings</h2>
            <p class="text-muted mb-0">Manage your account information and preferences.</p>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Please correct the errors below.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- ==========================================
            LEFT COLUMN (PROFILE CARD & ACTIVITY)
        ========================================== --}}
        <div class="col-lg-4 fade-in">
            {{-- Profile Card --}}
            <div class="card enterprise-card mb-4 text-center border-0">
                <div class="card-body p-4">
                    <div class="position-relative d-inline-block mb-3 mt-2">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #fff;">
                        @else
                            <div class="rounded-circle shadow-sm bg-soft-orange text-orange d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; border: 4px solid #fff;">
                                <i class="bi bi-person-fill" style="font-size: 60px;"></i>
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width: 20px; height: 20px; right: 10px; bottom: 5px;"></span>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->company_name ?? 'Member' }}</p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-globe-americas me-1 text-muted"></i> {{ $user->country ?? 'Not Set' }}</span>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-calendar3 me-1 text-muted"></i> Joined {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Activity Card --}}
            <div class="card enterprise-card border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-activity text-orange me-2"></i> Account Activity</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold">Trade Planner History</span>
                        <span class="badge bg-soft-orange text-orange rounded-pill px-3">{{ $tradePlannerHistory }} Simulations</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold">Watch List Count</span>
                        <span class="badge bg-soft-info text-info rounded-pill px-3">{{ $watchListCount }} Items</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold">Reports Generated</span>
                        <span class="badge bg-soft-success text-success rounded-pill px-3">{{ $reportsGenerated }} Reports</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light mt-3">
                        <span class="text-muted small fw-semibold">Last Login</span>
                        <span class="text-dark small fw-bold">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==========================================
            RIGHT COLUMN (TABS & FORMS)
        ========================================== --}}
        <div class="col-lg-8 fade-in" style="animation-delay: 0.1s;">
            <div class="card enterprise-card border-0 h-100">
                <div class="card-header bg-white border-bottom pt-4 px-4">
                    <ul class="nav nav-tabs card-header-tabs enterprise-tabs border-0" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab"><i class="bi bi-person-lines-fill me-2"></i> Account Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab"><i class="bi bi-shield-lock-fill me-2"></i> Security</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" id="avatar-tab" data-bs-toggle="tab" data-bs-target="#avatar" type="button" role="tab"><i class="bi bi-camera-fill me-2"></i> Profile Photo</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences" type="button" role="tab"><i class="bi bi-sliders me-2"></i> Preferences</button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4 p-lg-5">
                    <div class="tab-content" id="profileTabsContent">
                        
                        {{-- ACCOUNT TAB --}}
                        <div class="tab-pane fade show active" id="account" role="tabpanel">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control enterprise-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control enterprise-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Company Name</label>
                                        <input type="text" name="company_name" class="form-control enterprise-input" value="{{ old('company_name', $user->company_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Country</label>
                                        <input type="text" name="country" class="form-control enterprise-input" value="{{ old('country', $user->country) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Phone Number</label>
                                        <input type="text" name="phone" class="form-control enterprise-input" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-light">
                                    <button type="reset" class="btn btn-light rounded-pill px-4 fw-bold">Cancel</button>
                                    <button type="submit" class="btn btn-orange rounded-pill px-4 fw-bold shadow-sm">Save Changes</button>
                                </div>
                            </form>
                        </div>

                        {{-- SECURITY TAB --}}
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <form action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" name="old_password" class="form-control enterprise-input @error('old_password') is-invalid @enderror" required>
                                    @error('old_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">New Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control enterprise-input @error('password') is-invalid @enderror" required>
                                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Confirm New Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control enterprise-input" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-light">
                                    <button type="submit" class="btn btn-orange rounded-pill px-4 fw-bold shadow-sm">Update Password</button>
                                </div>
                            </form>
                        </div>

                        {{-- AVATAR TAB --}}
                        <div class="tab-pane fade" id="avatar" role="tabpanel">
                            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4 text-center p-5 border border-2 border-dashed rounded-4 bg-light">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-muted mb-3 d-block"></i>
                                    <h6 class="fw-bold text-dark">Upload Profile Photo</h6>
                                    <p class="text-muted small mb-4">JPEG, PNG or GIF (Max. 2MB)</p>
                                    <input type="file" name="avatar" class="form-control enterprise-input d-none" id="avatarInput" accept="image/*" required>
                                    <label for="avatarInput" class="btn btn-light border fw-bold rounded-pill px-4 shadow-sm cursor-pointer">Choose File</label>
                                    <div id="fileName" class="mt-3 text-muted small fw-semibold"></div>
                                    @error('avatar')<div class="text-danger small mt-2 fw-semibold">{{ $message }}</div>@enderror
                                </div>
                                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-light">
                                    <button type="submit" class="btn btn-orange rounded-pill px-4 fw-bold shadow-sm">Save Photo</button>
                                </div>
                            </form>
                        </div>

                        {{-- PREFERENCES TAB --}}
                        <div class="tab-pane fade" id="preferences" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Language</label>
                                <select class="form-select enterprise-input">
                                    <option value="en" selected>English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Theme</label>
                                <div class="d-flex gap-3 mt-2">
                                    <div class="form-check border rounded-3 p-3 flex-fill text-center bg-white shadow-sm cursor-pointer">
                                        <input class="form-check-input float-none mx-auto d-block mb-2" type="radio" name="theme" id="themeLight" checked>
                                        <label class="form-check-label fw-bold text-dark" for="themeLight">Light</label>
                                    </div>
                                    <div class="form-check border rounded-3 p-3 flex-fill text-center bg-dark text-white cursor-pointer opacity-50">
                                        <input class="form-check-input float-none mx-auto d-block mb-2" type="radio" name="theme" id="themeDark" disabled>
                                        <label class="form-check-label fw-bold" for="themeDark">Dark (Pro)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Notifications</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notifEmail" checked>
                                    <label class="form-check-label ms-2" for="notifEmail">Email Notifications (Alerts & Reports)</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-light">
                                <button type="button" class="btn btn-orange rounded-pill px-4 fw-bold shadow-sm">Save Preferences</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
/* Enterprise Profile Styles */
.enterprise-card {
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.bg-soft-orange { background-color: rgba(245, 158, 11, 0.1); }
.text-orange { color: #F59E0B !important; }
.btn-orange { 
    background-color: #F59E0B; 
    color: white; 
    transition: all 0.3s ease;
}
.btn-orange:hover { 
    background-color: #d97706; 
    color: white; 
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3) !important;
}

.bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
.bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }

.enterprise-input {
    border-radius: 10px;
    border: 1px solid #dee2e6;
    padding: 12px 15px;
    font-weight: 500;
    transition: all 0.2s ease;
    background-color: #f8f9fa;
}
.enterprise-input:focus {
    background-color: #fff;
    border-color: #F59E0B;
    box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25);
}

.enterprise-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 15px 20px;
    transition: all 0.2s ease;
}
.enterprise-tabs .nav-link:hover {
    color: #F59E0B;
}
.enterprise-tabs .nav-link.active {
    color: #F59E0B;
    background: transparent;
    border-bottom: 2px solid #F59E0B;
}

.border-dashed {
    border-style: dashed !important;
}
.cursor-pointer {
    cursor: pointer;
}

/* Animations */
.fade-in {
    animation: fadeIn 0.6s ease-out forwards;
    opacity: 0;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const avatarInput = document.getElementById('avatarInput');
    const fileName = document.getElementById('fileName');
    if (avatarInput && fileName) {
        avatarInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                fileName.textContent = "Selected: " + e.target.files[0].name;
            } else {
                fileName.textContent = "";
            }
        });
    }

    // Keep active tab on refresh if errors exist
    @if($errors->has('old_password') || $errors->has('password'))
        var triggerEl = document.querySelector('#security-tab')
        var tab = new bootstrap.Tab(triggerEl)
        tab.show()
    @elseif($errors->has('avatar'))
        var triggerEl = document.querySelector('#avatar-tab')
        var tab = new bootstrap.Tab(triggerEl)
        tab.show()
    @endif
});
</script>
@endpush
