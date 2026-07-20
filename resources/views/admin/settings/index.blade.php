@extends('layouts.admin')

@section('title', 'Admin Settings')
@section('page_title', 'System Settings')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        {{-- Alerts --}}
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

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf

            {{-- General Settings Card --}}
            <div class="panel mb-4">
                <div class="panel-heading mb-4">
                    <h5 class="panel-title mb-0">General Settings</h5>
                    <p class="text-muted small mb-0">Manage core system information and localization.</p>
                </div>
                <div class="panel-body">
                    <div class="mb-3">
                        <label class="form-label">System Name <span class="text-danger">*</span></label>
                        <input type="text" name="system_name" class="form-control" value="{{ old('system_name', $settings['system_name'] ?? '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Timezone <span class="text-danger">*</span></label>
                        <select name="timezone" class="form-select" required>
                            <option value="Asia/Jakarta" {{ (old('timezone', $settings['timezone'] ?? '') == 'Asia/Jakarta') ? 'selected' : '' }}>Asia/Jakarta</option>
                            <option value="UTC" {{ (old('timezone', $settings['timezone'] ?? '') == 'UTC') ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ (old('timezone', $settings['timezone'] ?? '') == 'America/New_York') ? 'selected' : '' }}>America/New_York</option>
                            <option value="Europe/London" {{ (old('timezone', $settings['timezone'] ?? '') == 'Europe/London') ? 'selected' : '' }}>Europe/London</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Notification Settings Card --}}
            <div class="panel mb-4">
                <div class="panel-heading mb-4">
                    <h5 class="panel-title mb-0">Notification Settings</h5>
                    <p class="text-muted small mb-0">Configure which system alerts should be active.</p>
                </div>
                <div class="panel-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="risk_notification" name="risk_notification" value="1" {{ (old('risk_notification', $settings['risk_notification'] ?? '0') == '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="risk_notification">
                            <strong>Risk Alert Notification</strong><br>
                            <small class="text-muted">Receive alerts for high-risk trade routes and country incidents.</small>
                        </label>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="weather_notification" name="weather_notification" value="1" {{ (old('weather_notification', $settings['weather_notification'] ?? '0') == '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="weather_notification">
                            <strong>Weather Alert Notification</strong><br>
                            <small class="text-muted">Receive alerts for extreme weather conditions affecting ports.</small>
                        </label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="currency_notification" name="currency_notification" value="1" {{ (old('currency_notification', $settings['currency_notification'] ?? '0') == '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="currency_notification">
                            <strong>Currency Alert Notification</strong><br>
                            <small class="text-muted">Receive alerts for significant currency exchange fluctuations.</small>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4 py-2" style="background:#FF7A00; border-color:#FF7A00; font-weight: 500;">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-check-input:checked {
        background-color: #FF7A00;
        border-color: #FF7A00;
    }
</style>
@endsection
