@extends('layouts.app')

@section('title', 'Currency Detail')

@section('content')

<div class="container-fluid">

    {{-- ==========================================
    PAGE HEADER
    ========================================== --}}
    <div class="mb-4">

        <h1 class="fw-bold display-4 d-flex align-items-center">

            <i class="bi bi-currency-exchange text-warning me-3"></i>

            Currency Detail

        </h1>

        <p class="text-muted fs-5 mb-0">

            Detailed currency exchange information affecting international trade.

        </p>

    </div>


    {{-- ==========================================
    COUNTRY HEADER
    ========================================== --}}
    <div class="card dashboard-card mb-4">

        <div class="card-body p-4">

            <div class="row align-items-center">

                <div class="col-lg-8">

                    <div class="d-flex align-items-center">

                        <img src="{{ $currency->country->flag }}"
                            alt="{{ $currency->country->country_name }}"
                            class="rounded shadow-sm me-4"
                            style="width:110px;height:75px;object-fit:cover;">

                        <div>

                            <h2 class="fw-bold mb-1">
                                {{ $currency->country->country_name }}
                            </h2>

                            <p class="text-muted mb-0">
                                Real-time Currency Exchange Monitoring
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                    @php

                        $badgeClass = match($status){

                            'SAFE' => 'bg-success',

                            'WATCH' => 'bg-warning text-dark',

                            'RISK' => 'bg-danger',

                            default => 'bg-secondary',

                        };

                    @endphp

                    <span class="badge {{ $badgeClass }} fs-5 px-4 py-3 rounded-pill">

                        {{ $status }}

                    </span>

                </div>

            </div>

        </div>

    </div>

    {{-- ==========================================
    SUMMARY CARDS
    ========================================== --}}
    <div class="row g-4 mb-4">

        {{-- Exchange Rate --}}
        <div class="col-lg-3 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body p-4">

                    <small class="text-muted d-block mb-2">
                        Exchange Rate
                    </small>

                    <h2 class="fw-bold text-primary mb-0">

                        {{ number_format($currency->exchange_rate,2) }}

                    </h2>

                </div>

            </div>

        </div>

        {{-- Daily Change --}}
        <div class="col-lg-3 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body p-4">

                    <small class="text-muted d-block mb-2">
                        Daily Change
                    </small>

                    @php
                        $changeClass =
                            $currency->change_percentage >= 0
                            ? 'text-success'
                            : 'text-danger';
                    @endphp

                    <h2 class="fw-bold {{ $changeClass }} mb-0">

                        {{ number_format($currency->change_percentage,2) }}%

                    </h2>

                </div>

            </div>

        </div>

        {{-- Base Currency --}}
        <div class="col-lg-3 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body p-4">

                    <small class="text-muted d-block mb-2">
                        Base Currency
                    </small>

                    <h2 class="fw-bold mb-0">

                        {{ $currency->base_currency }}

                    </h2>

                </div>

            </div>

        </div>

        {{-- Target Currency --}}
        <div class="col-lg-3 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body p-4">

                    <small class="text-muted d-block mb-2">
                        Target Currency
                    </small>

                    <h2 class="fw-bold mb-0">

                        {{ $currency->target_currency }}

                    </h2>

                </div>

            </div>

        </div>

    </div>

{{-- ==========================================
    TRADE IMPACT
========================================== --}}

<div class="card dashboard-card mb-4">

    <div class="card-body p-5">

        <div class="row align-items-center">

            {{-- Icon --}}
            <div class="col-lg-2 text-center">

                <div class="icon-circle bg-warning-subtle mx-auto">

                    <i class="bi bi-box-seam-fill text-warning"></i>

                </div>

            </div>

            {{-- Content --}}
            <div class="col-lg-10">

                <small class="text-uppercase text-muted fw-semibold">

                    Trade Impact

                </small>

                <div class="d-flex align-items-center gap-3 mt-2">

                    <h1 class="fw-bold mb-0 text-{{ $impactColor }}">

                        {{ $impact }}

                    </h1>

                    <span class="badge bg-{{ $statusColor }} rounded-pill px-3 py-2">

                        {{ $status }}

                    </span>

                </div>

                <p class="text-muted fs-5 mt-3 mb-0">

                    {{ $analysisMessage }}

                </p>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    CURRENCY ANALYSIS
========================================== --}}

<div class="card dashboard-card mb-4">

    <div class="card-body p-5">

        <div class="d-flex align-items-center mb-4">

            <i class="bi bi-currency-exchange text-warning fs-2 me-3"></i>

            <div>

                <h2 class="fw-bold mb-1">

                    Currency Analysis

                </h2>

                <small class="text-muted">

                    Exchange rate assessment for international trade.

                </small>

            </div>

        </div>

        {{-- Analysis Highlight --}}
        <div class="alert alert-warning border-0 rounded-4 p-4 mb-5">

            <h4 class="fw-bold text-dark mb-2">

                {{ $analysisTitle }}

            </h4>

            <p class="mb-0 text-secondary">

                {{ $analysisMessage }}

            </p>

        </div>

        <div class="row">

            {{-- LEFT --}}
            <div class="col-lg-6">

                <div class="border rounded-4 p-4 h-100">

                    <h5 class="fw-bold mb-4">

                        <i class="bi bi-graph-up-arrow text-success me-2"></i>

                        Main Currency Factors

                    </h5>

                    <table class="table table-borderless align-middle mb-0">

                        <tr>
                            <td class="text-muted fw-semibold">Base Currency</td>
                            <td class="fw-bold text-end">
                                {{ $currency->base_currency }}
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted fw-semibold">Target Currency</td>
                            <td class="fw-bold text-end">
                                {{ $currency->target_currency }}
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted fw-semibold">Exchange Rate</td>
                            <td class="fw-bold text-primary text-end">
                                {{ number_format($currency->exchange_rate,2) }}
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted fw-semibold">Daily Change</td>
                            <td class="fw-bold text-end {{ $currency->change_percentage >= 0 ? 'text-success' : 'text-danger' }}">

                                {{ number_format($currency->change_percentage,2) }}%

                            </td>
                        </tr>

                    </table>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-6">

                <div class="border rounded-4 p-4 h-100">

                    <h5 class="fw-bold mb-4">

                        <i class="bi bi-bar-chart-line text-primary me-2"></i>

                        Trade Assessment

                    </h5>

                    <table class="table table-borderless align-middle mb-0">

                        <tr>

                            <td class="text-muted fw-semibold">

                                Stability

                            </td>

                            <td class="text-end">

                                <span class="badge bg-{{ $statusColor }} rounded-pill px-3 py-2">

                                    {{ $status }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Trade Impact

                            </td>

                            <td class="text-end">

                                <span class="badge bg-{{ $impactColor }} rounded-pill px-3 py-2">

                                    {{ $impact }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Recommendation

                            </td>

                            <td class="text-end">

                                <span class="badge bg-light border rounded-pill text-dark px-3 py-2">

                                    {{ $recommendation }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Stability Score

                            </td>

                            <td class="text-end">

                                <span class="fw-bold fs-5 text-success">

                                    {{ $stability }}%

                                </span>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    TRADE RECOMMENDATION
========================================== --}}

<div class="row g-4 mb-4">

    {{-- LEFT --}}
    <div class="col-lg-7">

        <div class="card dashboard-card h-100">

            <div class="card-body p-5">

                <div class="d-flex align-items-center mb-4">

                    <i class="bi bi-lightbulb-fill text-warning fs-2 me-3"></i>

                    <div>

                        <h2 class="fw-bold mb-1">

                            Trade Recommendation

                        </h2>

                        <small class="text-muted">

                            Business recommendations based on current currency conditions.

                        </small>

                    </div>

                </div>

                @if($recommendation=='Proceed')

                    <div class="alert alert-success rounded-4 border-0">

                        <h5 class="fw-bold mb-2">

                            Proceed International Transactions

                        </h5>

                        Currency remains stable for international trade.

                    </div>

                    @php
                        $items = [
                            'Continue export schedule',
                            'Maintain supplier payments',
                            'Monitor exchange rates periodically',
                            'Continue normal trading operations',
                        ];
                    @endphp

                @elseif($recommendation=='Monitor')

                    <div class="alert alert-warning rounded-4 border-0">

                        <h5 class="fw-bold mb-2">

                            Monitor Exchange Rate

                        </h5>

                        Small fluctuations detected.

                    </div>

                    @php
                        $items = [
                            'Monitor exchange rate daily',
                            'Review supplier quotations',
                            'Prepare hedging strategy',
                            'Watch market movement',
                        ];
                    @endphp

                @elseif($recommendation=='Review')

                    <div class="alert alert-danger rounded-4 border-0">

                        <h5 class="fw-bold mb-2">

                            Review Pricing Strategy

                        </h5>

                        Significant volatility may affect profitability.

                    </div>

                    @php
                        $items = [
                            'Review export pricing',
                            'Evaluate import costs',
                            'Contact logistics partners',
                            'Monitor central bank updates',
                        ];
                    @endphp

                @else

                    <div class="alert alert-danger rounded-4 border-0">

                        <h5 class="fw-bold mb-2">

                            Delay High Risk Transactions

                        </h5>

                        Currency volatility is extremely high.

                    </div>

                    @php
                        $items = [
                            'Delay non-essential payments',
                            'Suspend high-risk imports',
                            'Use hedging instruments',
                            'Wait for market stabilization',
                        ];
                    @endphp

                @endif

                <div class="row mt-4">

                    @foreach($items as $item)

                        <div class="col-md-6 mb-3">

                            <div class="border rounded-4 p-3 h-100">

                                <i class="bi bi-check-circle-fill text-success me-2"></i>

                                {{ $item }}

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

    {{-- RIGHT --}}
    <div class="col-lg-5">

        <div class="card dashboard-card h-100">

            <div class="card-body p-5 text-center">

                <div class="mb-4">

                    <i class="bi bi-speedometer2 text-warning"
                       style="font-size:60px;"></i>

                </div>

                <h2 class="fw-bold">

                    {{ $stability }}%

                </h2>

                <p class="text-muted">

                    Currency Stability Score

                </p>

                <div class="progress rounded-pill mb-4"
                     style="height:14px;">

                    <div class="progress-bar bg-{{ $statusColor }}"
                         style="width:{{ $stability }}%">

                    </div>

                </div>

                <table class="table table-borderless align-middle mb-0">

                    <tr>

                        <td class="text-muted">

                            Status

                        </td>

                        <td class="text-end">

                            <span class="badge bg-{{ $statusColor }} rounded-pill">

                                {{ $status }}

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Trade Impact

                        </td>

                        <td class="text-end">

                            <span class="badge bg-{{ $impactColor }} rounded-pill">

                                {{ $impact }}

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Recommendation

                        </td>

                        <td class="text-end fw-bold">

                            {{ $recommendation }}

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    BUSINESS IMPACT
========================================== --}}

<div class="card dashboard-card mb-4">

    <div class="card-body p-5">

        <div class="d-flex align-items-center mb-4">

            <i class="bi bi-briefcase-fill text-warning fs-2 me-3"></i>

            <div>

                <h2 class="fw-bold mb-1">

                    Business Impact

                </h2>

                <small class="text-muted">

                    Estimated business impact based on current exchange rate conditions.

                </small>

            </div>

        </div>

        <div class="row g-4">

            {{-- Import Cost --}}
            <div class="col-lg-3 col-md-6">

                <div class="impact-card h-100">

                    <div class="impact-icon bg-primary-subtle">

                        <i class="bi bi-box-arrow-in-down text-primary"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Import Cost

                    </small>

                    <h5 class="fw-bold mt-2">

                        @if($impact=='LOW')

                            <span class="badge bg-success rounded-pill px-3 py-2">

                                LOW

                            </span>

                        @elseif($impact=='MEDIUM')

                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">

                                MEDIUM

                            </span>

                        @else

                            <span class="badge bg-danger rounded-pill px-3 py-2">

                                HIGH

                            </span>

                        @endif

                    </h5>

                </div>

            </div>

            {{-- Export Profit --}}
            <div class="col-lg-3 col-md-6">

                <div class="impact-card h-100">

                    <div class="impact-icon bg-success-subtle">

                        <i class="bi bi-graph-up-arrow text-success"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Export Profit

                    </small>

                    <h5 class="fw-bold mt-2">

                        @if($status=='STABLE')

                            <span class="badge bg-success rounded-pill px-3 py-2">

                                STABLE

                            </span>

                        @elseif($status=='WATCH')

                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">

                                MONITOR

                            </span>

                        @else

                            <span class="badge bg-danger rounded-pill px-3 py-2">

                                VOLATILE

                            </span>

                        @endif

                    </h5>

                </div>

            </div>

            {{-- Supplier Payment --}}
            <div class="col-lg-3 col-md-6">

                <div class="impact-card h-100">

                    <div class="impact-icon bg-info-subtle">

                        <i class="bi bi-credit-card text-info"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Supplier Payment

                    </small>

                    <h5 class="fw-bold mt-2">

                        {{ $recommendation }}

                    </h5>

                </div>

            </div>

            {{-- Trade Competitiveness --}}
            <div class="col-lg-3 col-md-6">

                <div class="impact-card h-100">

                    <div class="impact-icon bg-warning-subtle">

                        <i class="bi bi-globe2 text-warning"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Trade Competitiveness

                    </small>

                    <h5 class="fw-bold mt-2 text-{{ $statusColor }}">

                        {{ $status }}

                    </h5>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    QUICK NOTES
========================================== --}}

<div class="card dashboard-card mb-4">

    <div class="card-body p-5">

        <div class="d-flex align-items-center mb-4">

            <i class="bi bi-journal-text text-warning fs-2 me-3"></i>

            <div>

                <h2 class="fw-bold mb-1">

                    Quick Notes

                </h2>

                <small class="text-muted">

                    Executive summary and business advice.

                </small>

            </div>

        </div>

        <div class="row g-4">

            {{-- Currency Summary --}}
            <div class="col-lg-6">

                <div class="note-card h-100">

                    <div class="d-flex align-items-center mb-3">

                        <div class="note-icon bg-primary-subtle">

                            <i class="bi bi-file-earmark-text text-primary"></i>

                        </div>

                        <h5 class="fw-bold ms-3 mb-0">

                            Currency Summary

                        </h5>

                    </div>

                    <p class="text-muted mb-0">

                        {{ $analysisMessage }}

                    </p>

                </div>

            </div>

            {{-- Business Advice --}}
            <div class="col-lg-6">

                <div class="note-card h-100">

                    <div class="d-flex align-items-center mb-3">

                        <div class="note-icon bg-success-subtle">

                            <i class="bi bi-lightbulb-fill text-success"></i>

                        </div>

                        <h5 class="fw-bold ms-3 mb-0">

                            Business Advice

                        </h5>

                    </div>

                    <p class="text-muted mb-0">

                        @switch($recommendation)

                            @case('Proceed')
                                Current exchange rate supports normal international trade operations.
                                @break

                            @case('Monitor')
                                Monitor exchange rate movements before executing high-value transactions.
                                @break

                            @case('Review')
                                Review pricing strategy and supplier contracts before new agreements.
                                @break

                            @default
                                Delay non-essential transactions until market conditions become more stable.

                        @endswitch

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    TIMELINE
========================================== --}}

<div class="card dashboard-card mb-4">

    <div class="card-body p-5">

        <div class="d-flex align-items-center mb-4">

            <i class="bi bi-clock-history text-warning fs-2 me-3"></i>

            <div>

                <h2 class="fw-bold mb-1">

                    Timeline

                </h2>

                <small class="text-muted">

                    Currency data recording history.

                </small>

            </div>

        </div>

        <div class="row g-4">

            <div class="col-lg-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-primary-subtle">

                        <i class="bi bi-calendar-check text-primary"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Recorded At

                    </small>

                    <h5 class="fw-bold mt-2 mb-0">

                        {{ optional($currency->recorded_at)->format('d M Y H:i') }}

                    </h5>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-success-subtle">

                        <i class="bi bi-arrow-repeat text-success"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Last Updated

                    </small>

                    <h5 class="fw-bold mt-2 mb-0">

                        {{ optional($currency->updated_at)->format('d M Y H:i') }}

                    </h5>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    SYSTEM INFORMATION
========================================== --}}

<div class="card dashboard-card mb-4">

    <div class="card-body p-5">

        <div class="d-flex align-items-center mb-4">

            <i class="bi bi-info-circle-fill text-warning fs-2 me-3"></i>

            <div>

                <h2 class="fw-bold mb-1">

                    System Information

                </h2>

                <small class="text-muted">

                    Currency monitoring module information.

                </small>

            </div>

        </div>

        <div class="row g-4">

            <div class="col-lg-3 col-md-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-warning-subtle">

                        <i class="bi bi-currency-exchange text-warning"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Module

                    </small>

                    <h6 class="fw-bold mt-2">

                        Currency Monitoring

                    </h6>

                </div>

            </div>

            <div class="col-lg-3 col-md-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-info-subtle">

                        <i class="bi bi-database-fill text-info"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Data Source

                    </small>

                    <h6 class="fw-bold mt-2">

                        Exchange Rate Database

                    </h6>

                </div>

            </div>

            <div class="col-lg-3 col-md-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-success-subtle">

                        <i class="bi bi-calendar3 text-success"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Generated

                    </small>

                    <h6 class="fw-bold mt-2">

                        {{ now()->format('d M Y') }}

                    </h6>

                </div>

            </div>

            <div class="col-lg-3 col-md-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-danger-subtle">

                        <i class="bi bi-clock text-danger"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Time

                    </small>

                    <h6 class="fw-bold mt-2">

                        {{ now()->format('H:i') }}

                    </h6>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    FOOTER
========================================== --}}

<div class="card dashboard-card">

    <div class="card-body p-4">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <div class="d-flex align-items-center">

                    <div class="info-icon bg-warning-subtle me-3">

                        <i class="bi bi-shield-check text-warning"></i>

                    </div>

                    <div>

                        <h5 class="fw-bold mb-1">

                            Global Trade Risk Intelligence Platform

                        </h5>

                        <div class="text-muted">

                            Currency Monitoring Module

                        </div>

                        <small class="text-muted">

                            Version 1.0

                        </small>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                <a href="{{ route('currency.index') }}"
                   class="btn btn-outline-secondary rounded-pill px-4 me-2">

                    <i class="bi bi-arrow-left me-1"></i>

                    Back

                </a>

                <button
                    onclick="window.print()"
                    class="btn btn-warning rounded-pill px-4">

                    <i class="bi bi-printer me-1"></i>

                    Print

                </button>

            </div>

        </div>

    </div>

</div>

</div>

@push('styles')

<style>

.dashboard-card{

    border:none;

    border-radius:22px;

    background:#fff;

    box-shadow:0 8px 28px rgba(0,0,0,.06);

    transition:.25s;

}

.dashboard-card:hover{

    transform:translateY(-4px);

    box-shadow:0 16px 36px rgba(0,0,0,.10);

}

.icon-circle{

    width:90px;

    height:90px;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:38px;

}

.impact-card{

    background:#fff;

    border:1px solid #edf2f7;

    border-radius:18px;

    padding:24px;

    text-align:center;

    transition:.25s;

}

.impact-card:hover{

    transform:translateY(-3px);

    box-shadow:0 10px 24px rgba(0,0,0,.08);

}

.impact-icon{

    width:70px;

    height:70px;

    margin:auto;

    border-radius:18px;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:28px;

}

.note-card{

    border:1px solid #edf2f7;

    border-radius:18px;

    padding:24px;

    transition:.25s;

    background:#fff;

}

.note-card:hover{

    transform:translateY(-3px);

    box-shadow:0 10px 24px rgba(0,0,0,.08);

}

.note-icon{

    width:55px;

    height:55px;

    border-radius:15px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:22px;

}

.info-card{

    background:#fff;

    border:1px solid #edf2f7;

    border-radius:18px;

    padding:24px;

    text-align:center;

    transition:.25s;

}

.info-card:hover{

    transform:translateY(-3px);

    box-shadow:0 10px 24px rgba(0,0,0,.08);

}

.info-icon{

    width:60px;

    height:60px;

    margin:auto;

    border-radius:16px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:24px;

}

.table td{

    padding:.85rem .5rem;

    vertical-align:middle;

}

.badge{

    font-weight:600;

    letter-spacing:.3px;

}

</style>

@endpush

@endsection