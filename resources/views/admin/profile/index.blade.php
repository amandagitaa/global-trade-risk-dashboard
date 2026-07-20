@extends('layouts.admin')

@section('title', 'Admin Profile')
@section('page_title', 'My Profile')

@section('content')
<div class="row">
    {{-- Alerts --}}
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    {{-- Left Column --}}
    <div class="col-md-4 mb-4">
        {{-- Profile Information --}}
        <div class="panel mb-4 text-center">
            <div class="panel-body py-5">
                <div class="mb-3">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Profile Photo" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #FF7A00;">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light shadow-sm text-secondary" style="width: 120px; height: 120px; border: 3px solid #FF7A00; font-size: 3rem;">
                            <i class="bi bi-person"></i>
                        </div>
                    @endif
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <span class="badge bg-dark px-3 py-2">Role: {{ ucfirst($user->role) }}</span>
                    @if($user->company_name)
                        <span class="badge bg-secondary px-3 py-2">Company: {{ $user->company_name }}</span>
                    @endif
                    @if($user->country)
                        <span class="badge bg-secondary px-3 py-2">Country: {{ $user->country }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Account Information --}}
        <div class="panel">
            <div class="panel-heading border-bottom pb-3 mb-3">
                <h5 class="panel-title mb-0">Account Information</h5>
            </div>
            <div class="panel-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Role:</span>
                    <strong>{{ ucfirst($user->role) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Account Created:</span>
                    <strong>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Last Login:</span>
                    <strong>{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-8">
        {{-- Edit Profile --}}
        <div class="panel mb-4">
            <div class="panel-heading border-bottom pb-3 mb-4">
                <h5 class="panel-title mb-0">Edit Profile</h5>
            </div>
            <div class="panel-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $user->company_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $user->country) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        <small class="text-muted">Allowed formats: jpeg, png, jpg, gif. Max size: 2MB.</small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="background:#FF7A00; border-color:#FF7A00; font-weight: 500;">
                            <i class="bi bi-save me-1"></i> Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="panel">
            <div class="panel-heading border-bottom pb-3 mb-4">
                <h5 class="panel-title mb-0">Change Password</h5>
            </div>
            <div class="panel-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" required minlength="8">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" required minlength="8">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-dark px-4 py-2" style="font-weight: 500;">
                            <i class="bi bi-shield-lock me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
