@extends('layouts.app')

@section('title', 'Login - Global Trade Intelligence')

@section('content')
<div class="login-wrapper">
    <div class="row g-0 h-100">
        
        {{-- ==========================================
            LEFT COLUMN (HERO SECTION)
        ========================================== --}}
        <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-center position-relative hero-section">
            <div class="hero-bg"></div>
            <div class="hero-content p-5 position-relative z-1 text-center">
                {{-- Modern SVG Illustration (embedded directly) --}}
                <div class="mb-5 fade-in-up">
                    <svg width="280" height="200" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="50" y="200" width="300" height="40" rx="10" fill="#F59E0B" />
                        <rect x="70" y="150" width="80" height="50" rx="5" fill="#fef3c7" />
                        <rect x="160" y="150" width="80" height="50" rx="5" fill="#fef3c7" />
                        <rect x="250" y="150" width="80" height="50" rx="5" fill="#fef3c7" />
                        <rect x="70" y="90" width="80" height="50" rx="5" fill="#fde68a" />
                        <rect x="160" y="90" width="80" height="50" rx="5" fill="#fde68a" />
                        <path d="M40 240 L360 240 L340 280 L60 280 Z" fill="#d97706" />
                        <circle cx="200" cy="180" r="120" stroke="#fcd34d" stroke-width="2" stroke-dasharray="10 5" fill="none" opacity="0.5"/>
                    </svg>
                </div>

                <h2 class="fw-bold mb-3 fade-in-up" style="animation-delay: 0.1s;">
                    <a href="{{ route('login') }}" class="text-decoration-none" style="color: #b45309;">
                        Global Trade <br> Intelligence Platform
                    </a>
                </h2>
                
                <p class="text-dark opacity-75 fs-5 mb-5 fade-in-up" style="animation-delay: 0.2s;">
                    Monitor international trade,<br>
                    analyze risks,<br>
                    and plan shipments efficiently.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3 fade-in-up" style="animation-delay: 0.3s;">
                    <span class="badge bg-white text-dark shadow-sm border py-2 px-3 rounded-pill">
                        <i class="bi bi-globe-americas text-warning me-1"></i> Global Monitoring
                    </span>
                    <span class="badge bg-white text-dark shadow-sm border py-2 px-3 rounded-pill">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Risk Analysis
                    </span>
                    <span class="badge bg-white text-dark shadow-sm border py-2 px-3 rounded-pill">
                        <i class="bi bi-ship text-primary me-1"></i> Trade Planner
                    </span>
                </div>
            </div>
        </div>

        {{-- ==========================================
            RIGHT COLUMN (LOGIN FORM)
        ========================================== --}}
        <div class="col-lg-7 d-flex align-items-center justify-content-center bg-light">
            <div class="login-card p-4 p-md-5 w-100 fade-in" style="max-width: 500px;">
                
                {{-- Header --}}
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-orange rounded-circle p-3 mb-3 shadow-sm" style="width: 70px; height: 70px;">
                        <i class="bi bi-box-seam fs-2 text-orange"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">Welcome Back</h2>
                    <p class="text-muted small">Sign in to your account to continue</p>
                </div>

                {{-- Success Flash Message --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Alert for Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill fs-5 me-3"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div class="fw-semibold small">{{ $error }}</div>
                            @endforeach
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="https://global-trade-risk-dashboard-production.up.railway.app/login" id="loginForm">
                    @csrf
                    
                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold text-dark small text-uppercase">Email Address</label>
                        <div class="input-group input-group-lg premium-input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-4">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" id="email" 
                                   class="form-control border-start-0 ps-2 premium-input" 
                                   placeholder="Enter your email" 
                                   value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold text-dark small text-uppercase">Password</label>
                        <div class="input-group input-group-lg premium-input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-4">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" id="password" 
                                   class="form-control border-start-0 border-end-0 ps-2 premium-input" 
                                   placeholder="Enter your password" required>
                            <button class="btn btn-outline-secondary bg-white border-start-0 border text-muted pe-4" 
                                    type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Options --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted small fw-semibold" for="remember">
                                Remember Me
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-orange text-decoration-none small fw-bold">Forgot Password?</a>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-orange w-100 btn-lg rounded-pill fw-bold shadow-sm mb-3" id="submitBtn">
                        Sign In <i class="bi bi-arrow-right ms-2"></i>
                    </button>

                    <div class="text-center mt-3">
                        <span style="color: #6B7280; font-size: 15px; font-weight: 400;">
                            Don't have an account?
                        </span>

                        <a href="{{ route('register') }}"
                           class="create-account-link ms-1" style="color: #F59E0B; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.3s; text-decoration: none;">
                            Create Account
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Full Page Reset for Login */
    body {
        background: #f8f9fa;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .login-wrapper {
        min-height: 100vh;
        width: 100%;
        position: relative;
        z-index: 1050; /* Above sidebar */
    }

    /* Colors */
    .text-orange { color: #F59E0B !important; }
    .bg-soft-orange { background-color: rgba(245, 158, 11, 0.1); }
    .btn-orange { 
        background-color: #F59E0B; 
        color: white; 
        transition: all 0.3s ease;
    }
    .btn-orange:hover { 
        background-color: #d97706; 
        color: white; 
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3) !important;
    }
    .create-account-link:hover {
        text-decoration: underline !important;
        color: #D97706 !important;
    }

    /* Left Hero */
    .hero-section {
        background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%);
        overflow: hidden;
    }
    .hero-bg {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(245,158,11,0.05) 0%, rgba(255,255,255,0) 70%);
        animation: rotate 60s linear infinite;
    }
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Right Card */
    .login-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
    }

    /* Premium Inputs */
    .premium-input-group {
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
    }
    .premium-input-group:focus-within {
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15);
    }
    .premium-input-group .input-group-text,
    .premium-input-group .form-control,
    .premium-input-group .btn {
        border-color: #e5e7eb;
        background-color: #f9fafb;
    }
    .premium-input-group:focus-within .input-group-text,
    .premium-input-group:focus-within .form-control,
    .premium-input-group:focus-within .btn {
        border-color: #F59E0B;
        background-color: #ffffff;
    }
    .premium-input:focus {
        box-shadow: none;
    }
    .input-group-text i, .btn i {
        transition: color 0.3s ease;
    }
    .premium-input-group:focus-within .input-group-text i {
        color: #F59E0B !important;
    }

    /* Animations */
    .fade-in {
        animation: fadeIn 0.8s ease-out;
    }
    .fade-in-up {
        opacity: 0;
        animation: fadeInUp 0.8s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Show / Hide Password Toggle
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const toggleIcon = togglePassword.querySelector('i');

    togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        if (type === 'text') {
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
            toggleIcon.classList.add('text-orange');
        } else {
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
            toggleIcon.classList.remove('text-orange');
        }
    });

    const loginForm = document.querySelector('#loginForm');
    const submitBtn = document.querySelector('#submitBtn');

    loginForm.addEventListener('submit', function() {
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Signing In...';
        submitBtn.classList.add('disabled');
    });
});
</script>
@endpush
