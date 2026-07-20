@extends('layouts.app')

@section('title', 'Forgot Password - Global Trade Intelligence')

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
            <div class="login-card p-5 w-100 fade-in" style="max-width: 500px;">
                
                {{-- Header --}}
                <div class="text-center mb-5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-orange rounded-circle p-3 mb-3 shadow-sm" style="width: 70px; height: 70px;">
                        <i class="bi bi-key fs-2 text-orange"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Reset Password</h3>
                    <p class="text-muted">Enter your email and we'll send you a reset link.</p>
                </div>

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
                <form method="POST" action="{{ route('password.request') }}" id="loginForm">
                    @csrf
                    
                    {{-- Email --}}
                    <div class="mb-5">
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

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-orange w-100 btn-lg rounded-pill fw-bold shadow-sm mb-4" id="submitBtn">
                        Send Reset Link <i class="bi bi-arrow-right ms-2"></i>
                    </button>

                    {{-- Footer --}}
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-muted text-decoration-none small fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> Back to Login
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
        height: 100vh;
        overflow: hidden;
    }
    .login-wrapper {
        height: 100vh;
        width: 100vw;
        position: absolute;
        top: 0;
        left: 0;
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
    loginForm.addEventListener('submit', function() {
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...';
        submitBtn.classList.add('disabled');
    });
});
</script>
@endpush
