@extends('layouts.app')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | STATUS COLOR
    |--------------------------------------------------------------------------
    */

    $statusColor = 'success';

    switch($weather->weather_status){

        case 'rain':
            $statusColor='primary';
            break;

        case 'storm':
            $statusColor='warning';
            break;

        case 'extreme':
            $statusColor='danger';
            break;

    }

    /*
    |--------------------------------------------------------------------------
    | TRADE IMPACT
    |--------------------------------------------------------------------------
    */

    if($weather->storm_risk <= 25){

        $impact = 'SAFE';
        $impactColor = 'success';

    }

    elseif($weather->storm_risk <= 50){

        $impact = 'LOW';
        $impactColor = 'primary';

    }

    elseif($weather->storm_risk <= 75){

        $impact = 'MEDIUM';
        $impactColor = 'warning';

    }

    else{

        $impact = 'HIGH';
        $impactColor = 'danger';

    }

@endphp


<div class="container-fluid">

    {{-- ========================================================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold display-4 mb-2">

                🌦 Weather Detail

            </h1>

            <p class="text-muted fs-5 mb-0">

                Detailed weather information affecting international trade.

            </p>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- COUNTRY HEADER --}}
    {{-- ========================================================= --}}

    <div class="card dashboard-card border-0 shadow-sm mb-4">

        <div class="card-body py-4">

            <div class="row align-items-center">

                <div class="col-lg-1 text-center">

                    <img
                        src="{{ $weather->country->flag }}"
                        class="rounded shadow"
                        width="70">

                </div>

                <div class="col-lg-8">

                    <h2 class="fw-bold mb-1">

                        {{ $weather->country->country_name }}

                    </h2>

                    <div class="text-muted">

                        Real-time Weather Monitoring Result

                    </div>

                </div>

                <div class="col-lg-3 text-end">

                    <span class="badge bg-{{ $statusColor }} fs-5 px-4 py-3">

                        @switch($weather->weather_status)

                            @case('clear')

                                ☀ CLEAR

                                @break

                            @case('rain')

                                🌧 RAIN

                                @break

                            @case('storm')

                                ⛈ STORM

                                @break

                            @default

                                🔥 EXTREME

                        @endswitch

                    </span>

                </div>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- SUMMARY CARDS --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mb-4">

    {{-- ===========================
    CARD 1
=========================== --}}

<div class="col-xl col-lg-4 col-md-6">

    <div class="card dashboard-card border-0 shadow-sm h-100">

        <div class="card-body">

            <div class="d-flex align-items-center mb-3">

                <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">

                    <i class="bi bi-thermometer-half text-warning fs-3"></i>

                </div>

                <div>

                    <small class="text-muted">
                        Temperature
                    </small>

                    <h2 class="fw-bold text-warning mb-0">

                        {{ number_format($weather->temperature,1) }}°C

                    </h2>

                </div>

            </div>

            <small class="text-muted">

                @if($weather->temperature < 15)

                    Cold Weather

                @elseif($weather->temperature <= 30)

                    Normal Temperature

                @else

                    Hot Weather

                @endif

            </small>

        </div>

    </div>

</div>



{{-- ===========================
    CARD 2
=========================== --}}

<div class="col-xl col-lg-4 col-md-6">

    <div class="card dashboard-card border-0 shadow-sm h-100">

        <div class="card-body">

            <div class="d-flex align-items-center mb-3">

                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">

                    <i class="bi bi-cloud-rain-heavy text-primary fs-3"></i>

                </div>

                <div>

                    <small class="text-muted">

                        Rainfall

                    </small>

                    <h2 class="fw-bold text-primary mb-0">

                        {{ number_format($weather->rainfall,1) }} mm

                    </h2>

                </div>

            </div>

            <small class="text-muted">

                @if($weather->rainfall < 50)

                    Light Rain

                @elseif($weather->rainfall < 150)

                    Moderate Rain

                @else

                    Heavy Rain

                @endif

            </small>

        </div>

    </div>

</div>



{{-- ===========================
    CARD 3
=========================== --}}

<div class="col-xl col-lg-4 col-md-6">

    <div class="card dashboard-card border-0 shadow-sm h-100">

        <div class="card-body">

            <div class="d-flex align-items-center mb-3">

                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">

                    <i class="bi bi-wind text-success fs-3"></i>

                </div>

                <div>

                    <small class="text-muted">

                        Wind Speed

                    </small>

                    <h2 class="fw-bold text-success mb-0">

                        {{ number_format($weather->wind_speed,1) }} km/h

                    </h2>

                </div>

            </div>

            <small class="text-muted">

                @if($weather->wind_speed < 20)

                    Calm Wind

                @elseif($weather->wind_speed < 40)

                    Moderate Wind

                @else

                    Strong Wind

                @endif

            </small>

        </div>

    </div>

</div>



{{-- ===========================
    CARD 4
=========================== --}}

<div class="col-xl col-lg-6 col-md-6">

    <div class="card dashboard-card border-0 shadow-sm h-100">

        <div class="card-body">

            <div class="d-flex align-items-center mb-3">

                <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">

                    <i class="bi bi-cloud-lightning-rain text-danger fs-3"></i>

                </div>

                <div>

                    <small class="text-muted">

                        Storm Risk

                    </small>

                    <h2 class="fw-bold text-danger mb-0">

                        {{ $weather->storm_risk }}%

                    </h2>

                </div>

            </div>

            <small class="text-muted">

                @if($weather->storm_risk<=25)

                    Low Probability

                @elseif($weather->storm_risk<=50)

                    Moderate Probability

                @elseif($weather->storm_risk<=75)

                    High Probability

                @else

                    Extreme Probability

                @endif

            </small>

        </div>

    </div>

</div>



{{-- ===========================
    CARD 5
=========================== --}}

<div class="col-xl col-lg-6 col-md-12">

    <div class="card dashboard-card border-0 shadow-sm h-100">

        <div class="card-body">

            <div class="d-flex align-items-center mb-3">

                <div class="bg-{{ $impactColor }} bg-opacity-10 rounded-circle p-3 me-3">

                    <i class="bi bi-box-seam text-{{ $impactColor }} fs-3"></i>

                </div>

                <div>

                    <small class="text-muted">

                        Trade Impact

                    </small>

                    <h2 class="fw-bold mb-0">

                        <span class="badge bg-{{ $impactColor }}">

                            {{ $impact }}

                        </span>

                    </h2>

                </div>

            </div>

            <small class="text-muted">

                Shipment Recommendation

            </small>

        </div>

    </div>

</div>

</div>

{{-- ========================================================= --}}
{{-- WEATHER ANALYSIS --}}
{{-- ========================================================= --}}

<div class="card dashboard-card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 py-3">

        <h4 class="fw-bold mb-0">

            🌦 Weather Analysis

        </h4>

    </div>

    <div class="card-body">

        @php

            switch($weather->weather_status){

                case 'clear':

                    $analysisTitle='Excellent Weather';

                    $analysisColor='success';

                    $analysisIcon='☀';

                    $analysisText='Weather conditions are highly favorable for international logistics, shipping, and cross-border trade operations.';

                break;

                case 'rain':

                    $analysisTitle='Rain Detected';

                    $analysisColor='primary';

                    $analysisIcon='🌧';

                    $analysisText='Rainfall may slightly affect transportation schedules. Cargo movement remains operational but should be monitored.';

                break;

                case 'storm':

                    $analysisTitle='Storm Warning';

                    $analysisColor='warning';

                    $analysisIcon='⛈';

                    $analysisText='Storm conditions may delay port operations, shipping schedules, and inland transportation.';

                break;

                default:

                    $analysisTitle='Extreme Weather';

                    $analysisColor='danger';

                    $analysisIcon='🔥';

                    $analysisText='Extreme weather poses a significant risk to logistics networks and international trade activities.';

            }

        @endphp

        <div class="alert alert-{{ $analysisColor }} border-0">

            <h4 class="fw-bold mb-2">

                {{ $analysisIcon }}

                {{ $analysisTitle }}

            </h4>

            <p class="mb-0">

                {{ $analysisText }}

            </p>

        </div>

        <div class="row mt-4">

            <div class="col-lg-6">

                <h5 class="fw-bold mb-3">

                    Main Weather Factors

                </h5>

                <table class="table table-borderless align-middle">

                    <tr>

                        <td width="45%">

                            🌡 Temperature

                        </td>

                        <td class="fw-bold">

                            {{ number_format($weather->temperature,1) }} °C

                        </td>

                    </tr>

                    <tr>

                        <td>

                            🌧 Rainfall

                        </td>

                        <td class="fw-bold">

                            {{ number_format($weather->rainfall,1) }} mm

                        </td>

                    </tr>

                    <tr>

                        <td>

                            🌬 Wind Speed

                        </td>

                        <td class="fw-bold">

                            {{ number_format($weather->wind_speed,1) }} km/h

                        </td>

                    </tr>

                </table>

            </div>

            <div class="col-lg-6">

                <h5 class="fw-bold mb-3">

                    Trade Assessment

                </h5>

                <table class="table table-borderless align-middle">

                    <tr>

                        <td width="45%">

                            ⚡ Storm Risk

                        </td>

                        <td class="fw-bold text-danger">

                            {{ $weather->storm_risk }}%

                        </td>

                    </tr>

                    <tr>

                        <td>

                            🌤 Weather Status

                        </td>

                        <td>

                            <span class="badge bg-{{ $statusColor }} px-3 py-2">

                                {{ strtoupper($weather->weather_status) }}

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td>

                            🚢 Trade Impact

                        </td>

                        <td>

                            <span class="badge bg-{{ $impactColor }} px-3 py-2">

                                {{ $impact }}

                            </span>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

        <hr class="my-4">

        <div class="row text-center">

            <div class="col-md-3">

                <i class="bi bi-thermometer-half text-warning fs-2"></i>

                <div class="fw-bold mt-2">

                    Temperature

                </div>

                <small class="text-muted">

                    {{ number_format($weather->temperature,1) }}°C

                </small>

            </div>

            <div class="col-md-3">

                <i class="bi bi-cloud-rain-heavy text-primary fs-2"></i>

                <div class="fw-bold mt-2">

                    Rainfall

                </div>

                <small class="text-muted">

                    {{ number_format($weather->rainfall,1) }} mm

                </small>

            </div>

            <div class="col-md-3">

                <i class="bi bi-wind text-success fs-2"></i>

                <div class="fw-bold mt-2">

                    Wind Speed

                </div>

                <small class="text-muted">

                    {{ number_format($weather->wind_speed,1) }} km/h

                </small>

            </div>

            <div class="col-md-3">

                <i class="bi bi-cloud-lightning-rain text-danger fs-2"></i>

                <div class="fw-bold mt-2">

                    Storm Risk

                </div>

                <small class="text-muted">

                    {{ $weather->storm_risk }}%

                </small>

            </div>

        </div>

    </div>

</div>



{{-- ========================================================= --}}
{{-- TRADE RECOMMENDATION --}}
{{-- ========================================================= --}}

<div class="row g-4 mb-4">

    {{-- =========================================
        TRADE RECOMMENDATION
    ========================================== --}}

    <div class="col-lg-7">

        <div class="card dashboard-card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0 py-3">

                <h4 class="fw-bold mb-0">

                    💡 Trade Recommendation

                </h4>

            </div>

            <div class="card-body">

                @if($weather->storm_risk <= 25)

                    <div class="alert alert-success border-0">

                        <h5 class="fw-bold">

                            ✅ Safe To Trade

                        </h5>

                        <p class="mb-0">

                            Weather conditions are ideal for international trade operations.

                        </p>

                    </div>

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item">
                            ✓ Continue export & import activities
                        </li>

                        <li class="list-group-item">
                            ✓ Normal shipping schedule
                        </li>

                        <li class="list-group-item">
                            ✓ Port operation remains stable
                        </li>

                        <li class="list-group-item">
                            ✓ Logistics risk is minimal
                        </li>

                    </ul>

                @elseif($weather->storm_risk <= 50)

                    <div class="alert alert-primary border-0">

                        <h5 class="fw-bold">

                            ℹ Maintain Trade

                        </h5>

                        <p class="mb-0">

                            Trade activities can continue while monitoring weather updates.

                        </p>

                    </div>

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item">
                            ✓ Monitor weather every few hours
                        </li>

                        <li class="list-group-item">
                            ✓ Continue shipment schedule
                        </li>

                        <li class="list-group-item">
                            ✓ Prepare backup logistics
                        </li>

                        <li class="list-group-item">
                            ✓ Inform shipping partners
                        </li>

                    </ul>

                @elseif($weather->storm_risk <= 75)

                    <div class="alert alert-warning border-0">

                        <h5 class="fw-bold">

                            ⚠ Monitor Carefully

                        </h5>

                        <p class="mb-0">

                            High weather risk may impact logistics and maritime transportation.

                        </p>

                    </div>

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item">
                            ✓ Monitor port congestion
                        </li>

                        <li class="list-group-item">
                            ✓ Review shipment priority
                        </li>

                        <li class="list-group-item">
                            ✓ Delay sensitive cargo if needed
                        </li>

                        <li class="list-group-item">
                            ✓ Activate contingency plan
                        </li>

                    </ul>

                @else

                    <div class="alert alert-danger border-0">

                        <h5 class="fw-bold">

                            ⛔ Delay High Risk Shipment

                        </h5>

                        <p class="mb-0">

                            Extreme weather conditions may severely disrupt global trade.

                        </p>

                    </div>

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item">
                            ✓ Delay non-essential shipment
                        </li>

                        <li class="list-group-item">
                            ✓ Notify logistics partners
                        </li>

                        <li class="list-group-item">
                            ✓ Monitor emergency updates
                        </li>

                        <li class="list-group-item">
                            ✓ Review business continuity plan
                        </li>

                    </ul>

                @endif

            </div>

        </div>

    </div>

    {{-- =========================================
        RISK INDICATOR
    ========================================== --}}

    <div class="col-lg-5">

        <div class="card dashboard-card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0 py-3">

                <h4 class="fw-bold mb-0">

                    📊 Risk Indicator

                </h4>

            </div>

            <div class="card-body">

                <div class="text-center mb-4">

                    <h1 class="display-4 fw-bold text-{{ $impactColor }}">

                        {{ $weather->storm_risk }}%

                    </h1>

                    <div class="text-muted">

                        Current Storm Risk

                    </div>

                </div>

                <div class="progress mb-4"
                     style="height:20px;">

                    <div class="progress-bar bg-{{ $impactColor }}"
                         style="width: {{ $weather->storm_risk }}%;">

                    </div>

                </div>

                <div class="d-flex justify-content-between small text-muted">

                    <span>0%</span>

                    <span>25%</span>

                    <span>50%</span>

                    <span>75%</span>

                    <span>100%</span>

                </div>

                <hr>

                <table class="table table-borderless mb-0">

                    <tr>

                        <td>

                            Weather Status

                        </td>

                        <td class="text-end">

                            <span class="badge bg-{{ $statusColor }}">

                                {{ strtoupper($weather->weather_status) }}

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td>

                            Trade Impact

                        </td>

                        <td class="text-end">

                            <span class="badge bg-{{ $impactColor }}">

                                {{ $impact }}

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td>

                            Recommendation

                        </td>

                        <td class="text-end fw-bold">

                            @if($weather->storm_risk<=25)

                                Safe

                            @elseif($weather->storm_risk<=50)

                                Monitor

                            @elseif($weather->storm_risk<=75)

                                Caution

                            @else

                                Delay

                            @endif

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

{{-- ========================================================= --}}
{{-- BUSINESS IMPACT --}}
{{-- ========================================================= --}}

<div class="card dashboard-card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 py-3">

        <h4 class="fw-bold mb-0">

            📦 Business Impact

        </h4>

    </div>

    <div class="card-body">

        <div class="row g-4">

            {{-- Port Operation --}}
            <div class="col-md-6">

                <div class="card border-0 bg-light h-100">

                    <div class="card-body text-center">

                        <div class="display-6 mb-2">
                            🚢
                        </div>

                        <h5 class="fw-bold">

                            Port Operation

                        </h5>

                        <h4 class="mt-3">

                            @if($weather->storm_risk <=40)

                                <span class="badge bg-success">

                                    NORMAL

                                </span>

                            @elseif($weather->storm_risk<=70)

                                <span class="badge bg-warning">

                                    MODERATE

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    DISRUPTED

                                </span>

                            @endif

                        </h4>

                    </div>

                </div>

            </div>



            {{-- Logistics --}}
            <div class="col-md-6">

                <div class="card border-0 bg-light h-100">

                    <div class="card-body text-center">

                        <div class="display-6 mb-2">
                            🚚
                        </div>

                        <h5 class="fw-bold">

                            Logistics

                        </h5>

                        <h4 class="mt-3">

                            @if($weather->storm_risk <=40)

                                <span class="badge bg-success">

                                    SMOOTH

                                </span>

                            @elseif($weather->storm_risk<=70)

                                <span class="badge bg-warning">

                                    DELAY

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    HIGH DELAY

                                </span>

                            @endif

                        </h4>

                    </div>

                </div>

            </div>



            {{-- Cargo --}}
            <div class="col-md-6">

                <div class="card border-0 bg-light h-100">

                    <div class="card-body text-center">

                        <div class="display-6 mb-2">
                            📦
                        </div>

                        <h5 class="fw-bold">

                            Cargo Handling

                        </h5>

                        <h4 class="mt-3">

                            @if($weather->storm_risk<=40)

                                <span class="badge bg-success">

                                    SAFE

                                </span>

                            @elseif($weather->storm_risk<=70)

                                <span class="badge bg-warning">

                                    MONITOR

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    HIGH RISK

                                </span>

                            @endif

                        </h4>

                    </div>

                </div>

            </div>



            {{-- Trade Activity --}}
            <div class="col-md-6">

                <div class="card border-0 bg-light h-100">

                    <div class="card-body text-center">

                        <div class="display-6 mb-2">
                            🌍
                        </div>

                        <h5 class="fw-bold">

                            Trade Activity

                        </h5>

                        <h4 class="mt-3">

                            <span class="badge bg-{{ $impactColor }}">

                                {{ $impact }}

                            </span>

                        </h4>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ========================================================= --}}
{{-- QUICK NOTES --}}
{{-- ========================================================= --}}

<div class="card dashboard-card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 py-3">

        <h4 class="fw-bold mb-0">

            📝 Quick Notes

        </h4>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-lg-6">

                <div class="alert alert-light border h-100">

                    <h5 class="fw-bold">

                        Weather Summary

                    </h5>

                    <p class="mb-0">

                        {{ $analysisText }}

                    </p>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="alert alert-light border h-100">

                    <h5 class="fw-bold">

                        Business Advice

                    </h5>

                    <p class="mb-0">

                        @if($weather->storm_risk<=25)

                            Current weather supports stable international logistics and shipping.

                        @elseif($weather->storm_risk<=50)

                            Continue monitoring weather updates before shipment.

                        @elseif($weather->storm_risk<=75)

                            Consider rescheduling sensitive shipments if conditions worsen.

                        @else

                            Delay high-value cargo until weather conditions improve.

                        @endif

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ========================================================= --}}
{{-- WEATHER TIMELINE --}}
{{-- ========================================================= --}}

<div class="card dashboard-card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 py-3">

        <h4 class="fw-bold mb-0">

            🕒 Weather Timeline

        </h4>

    </div>

    <div class="card-body">

        <div class="row text-center">

            <div class="col-md-6">

                <small class="text-muted">

                    Recorded At

                </small>

                <h5 class="fw-bold mt-2">

                    {{ optional($weather->recorded_at)->format('d M Y H:i') ?? '-' }}

                </h5>

            </div>

            <div class="col-md-6">

                <small class="text-muted">

                    Last Updated

                </small>

                <h5 class="fw-bold mt-2">

                    {{ optional($weather->updated_at)->format('d M Y H:i') }}

                </h5>

            </div>

        </div>

    </div>

</div>

{{-- ========================================================= --}}
{{-- SYSTEM INFORMATION --}}
{{-- ========================================================= --}}

<div class="card dashboard-card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 py-3">

        <h4 class="fw-bold mb-0">

            ℹ System Information

        </h4>

    </div>

    <div class="card-body">

        <div class="row text-center">

            <div class="col-lg-3 col-md-6 mb-3">

                <div class="border rounded-4 p-4 h-100">

                    <div class="display-6 mb-2">

                        🌦

                    </div>

                    <div class="fw-bold">

                        Module

                    </div>

                    <div class="text-muted">

                        Weather Monitoring

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6 mb-3">

                <div class="border rounded-4 p-4 h-100">

                    <div class="display-6 mb-2">

                        🛰

                    </div>

                    <div class="fw-bold">

                        Data Source

                    </div>

                    <div class="text-muted">

                        Weather Database

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6 mb-3">

                <div class="border rounded-4 p-4 h-100">

                    <div class="display-6 mb-2">

                        📅

                    </div>

                    <div class="fw-bold">

                        Generated

                    </div>

                    <div class="text-muted">

                        {{ now()->format('d M Y') }}

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6 mb-3">

                <div class="border rounded-4 p-4 h-100">

                    <div class="display-6 mb-2">

                        🕒

                    </div>

                    <div class="fw-bold">

                        Time

                    </div>

                    <div class="text-muted">

                        {{ now()->format('H:i') }}

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ========================================================= --}}
{{-- FOOTER --}}
{{-- ========================================================= --}}

<div class="card dashboard-card border-0 shadow-sm">

    <div class="card-body">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <h5 class="fw-bold mb-2">

                    Global Trade Risk Intelligence Platform

                </h5>

                <div class="text-muted">

                    Weather Monitoring Module

                </div>

                <div class="text-muted">

                    Version 1.0

                </div>

            </div>

            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">

                <a href="{{ route('weather.index') }}"
                   class="btn btn-outline-secondary me-2">

                    <i class="bi bi-arrow-left"></i>

                    Back

                </a>

                <button
                    onclick="window.print()"
                    class="btn btn-warning">

                    <i class="bi bi-printer"></i>

                    Print

                </button>

            </div>

        </div>

    </div>

</div>

</div>

@endsection