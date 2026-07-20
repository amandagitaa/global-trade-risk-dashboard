@extends('layouts.app')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<div class="page-header">

    <div>

        <h1 class="page-title">

            <i class="bi bi-graph-up-arrow text-warning me-2"></i>

            Economy Detail

        </h1>

        <p class="page-subtitle">

            Detailed macroeconomic indicators affecting international trade.

        </p>

    </div>

</div>

{{-- ================= HERO ================= --}}
<div class="hero-card">

    <div class="hero-country">

        <img src="{{ $economy->country->flag }}"
             class="country-flag">

        <div>

            <h2 class="hero-title">
                {{ $economy->country->country_name }}
            </h2>

            <p class="hero-subtitle">
                Real-time Economic Monitoring
            </p>

        </div>

    </div>

    <div>

        @php

            if($economy->inflation <= 3){

                $status='STABLE';
                $badge='success';

            }elseif($economy->inflation <=6){

                $status='MODERATE';
                $badge='warning text-dark';

            }else{

                $status='HIGH RISK';
                $badge='danger';

            }

        @endphp

        <span class="badge bg-{{ $badge }} hero-status">

            {{ $status }}

        </span>

    </div>

</div>

{{-- ==========================================
    SUMMARY CARDS
========================================== --}}
<div class="row g-4 mb-4">

    {{-- GDP --}}
    <div class="col-lg-3 col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body p-4">

                <small class="text-muted d-block mb-2">

                    Gross Domestic Product

                </small>

                <h2 class="fw-bold text-primary mb-0">

                    {{ $economy->formatted_gdp }}

                </h2>

            </div>

        </div>

    </div>

    {{-- Inflation --}}
    <div class="col-lg-3 col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body p-4">

                <small class="text-muted d-block mb-2">

                    Inflation Rate

                </small>

                <h2 class="fw-bold text-danger mb-0">

                    {{ number_format($economy->inflation,2) }}%

                </h2>

            </div>

        </div>

    </div>

    {{-- Exports --}}
    <div class="col-lg-3 col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body p-4">

                <small class="text-muted d-block mb-2">

                    Total Exports

                </small>

                <h2 class="fw-bold text-success mb-0">

                    {{ $economy->formatted_exports }}

                </h2>

            </div>

        </div>

    </div>

    {{-- Imports --}}
    <div class="col-lg-3 col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body p-4">

                <small class="text-muted d-block mb-2">

                    Total Imports

                </small>

                <h2 class="fw-bold text-warning mb-0">

                    {{ $economy->formatted_imports }}

                </h2>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    ECONOMIC OVERVIEW
========================================== --}}

@php

    $growth = $economy->exports - $economy->imports;

    if($economy->inflation <= 2 && $growth > 0){

        $overview = 'Excellent';
        $overviewColor = 'success';

        $overviewMessage =
            'The economy demonstrates strong growth with low inflation and a positive trade balance. Current conditions support international trade expansion.';

    }elseif($economy->inflation <= 4){

        $overview = 'Good';
        $overviewColor = 'primary';

        $overviewMessage =
            'Economic conditions remain healthy. Inflation is under control and trade performance is generally stable.';

    }elseif($economy->inflation <= 6){

        $overview = 'Moderate';
        $overviewColor = 'warning';

        $overviewMessage =
            'Inflation has started to increase. Businesses should monitor macroeconomic conditions before making major decisions.';

    }else{

        $overview = 'Weak';
        $overviewColor = 'danger';

        $overviewMessage =
            'High inflation may reduce purchasing power and affect international trade competitiveness.';

    }

@endphp


<div class="card dashboard-card mb-4">

    <div class="card-body p-5">

        <div class="row align-items-center">

            <div class="col-lg-2 text-center">

                <div class="icon-circle bg-warning-subtle mx-auto">

                    <i class="bi bi-bar-chart-line-fill text-warning"></i>

                </div>

            </div>

            <div class="col-lg-10">

                <small class="text-uppercase text-muted fw-semibold">

                    Economic Overview

                </small>

                <div class="d-flex align-items-center gap-3 mt-2">

                    <h1 class="fw-bold mb-0 text-{{ $overviewColor }}">

                        {{ $overview }}

                    </h1>

                    <span class="badge bg-{{ $overviewColor }} rounded-pill px-3 py-2">

                        {{ $status }}

                    </span>

                </div>

                <p class="text-muted fs-5 mt-3 mb-0">

                    {{ $overviewMessage }}

                </p>

            </div>

        </div>

    </div>

</div>

{{-- ==========================================
    ECONOMIC ANALYSIS
========================================== --}}

<div class="card dashboard-card mb-4">

    <div class="card-body p-5">

        <div class="d-flex align-items-center mb-4">

            <i class="bi bi-bar-chart-line-fill text-warning fs-2 me-3"></i>

            <div>

                <h2 class="fw-bold mb-1">

                    Economic Analysis

                </h2>

                <small class="text-muted">

                    Comprehensive macroeconomic assessment for international trade.

                </small>

            </div>

        </div>

        {{-- Analysis Highlight --}}
        <div class="alert alert-warning border-0 rounded-4 p-4 mb-5">

            <h4 class="fw-bold text-dark mb-2">

                {{ $overview }} Economy

            </h4>

            <p class="mb-0 text-secondary">

                {{ $overviewMessage }}

            </p>

        </div>

        <div class="row">

            {{-- LEFT PANEL --}}
            <div class="col-lg-6">

                <div class="border rounded-4 p-4 h-100">

                    <h5 class="fw-bold mb-4">

                        <i class="bi bi-bank text-success me-2"></i>

                        Main Economic Indicators

                    </h5>

                    <table class="table table-borderless align-middle mb-0">

                        <tr>

                            <td class="text-muted fw-semibold">

                                GDP

                            </td>

                            <td class="fw-bold text-end">

                                {{ $economy->formatted_gdp }}

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Inflation

                            </td>

                            <td class="fw-bold text-danger text-end">

                                {{ number_format($economy->inflation,2) }}%

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Exports

                            </td>

                            <td class="fw-bold text-success text-end">

                                {{ $economy->formatted_exports }}

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Imports

                            </td>

                            <td class="fw-bold text-warning text-end">

                                {{ $economy->formatted_imports }}

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Data Year

                            </td>

                            <td class="fw-bold text-end">

                                {{ $economy->data_year }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            {{-- RIGHT PANEL --}}
            <div class="col-lg-6">

                <div class="border rounded-4 p-4 h-100">

                    <h5 class="fw-bold mb-4">

                        <i class="bi bi-clipboard-data text-primary me-2"></i>

                        Economic Assessment

                    </h5>

                    <table class="table table-borderless align-middle mb-0">

                        <tr>

                            <td class="text-muted fw-semibold">

                                Economic Status

                            </td>

                            <td class="text-end">

                                <span class="badge bg-{{ $overviewColor }} rounded-pill px-3 py-2">

                                    {{ $overview }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Inflation Risk

                            </td>

                            <td class="text-end">

                                <span class="badge bg-{{ $badge }} rounded-pill px-3 py-2">

                                    {{ $status }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Trade Balance

                            </td>

                            <td class="text-end">

                                @if(($economy->exports - $economy->imports) >= 0)

                                    <span class="badge bg-success rounded-pill">

                                        Surplus

                                    </span>

                                @else

                                    <span class="badge bg-danger rounded-pill">

                                        Deficit

                                    </span>

                                @endif

                            </td>

                        </tr>

                        <tr>

                            <td class="text-muted fw-semibold">

                                Overall Score

                            </td>

                            <td class="text-end">

                                <span class="fw-bold fs-5 text-success">

                                    {{ max(0, 100 - ($economy->inflation * 8)) }}%

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
    BUSINESS RECOMMENDATION
========================================== --}}

@php

    $tradeBalance = $economy->exports - $economy->imports;

    if($economy->inflation <= 2 && $tradeBalance > 0){

        $recommendation='Expand';

        $recommendationTitle='Expand International Business';

        $recommendationColor='success';

        $recommendationMessage='The current economic condition supports business expansion, export growth, and long-term investment.';

        $items=[

            'Increase export capacity',

            'Expand into new international markets',

            'Strengthen strategic investments',

            'Maintain current pricing strategy'

        ];

    }

    elseif($economy->inflation <=4){

        $recommendation='Maintain';

        $recommendationTitle='Maintain Current Operations';

        $recommendationColor='primary';

        $recommendationMessage='Economic conditions remain stable. Continue business operations while monitoring macroeconomic trends.';

        $items=[

            'Maintain export performance',

            'Monitor inflation trends',

            'Review supplier contracts',

            'Improve operational efficiency'

        ];

    }

    elseif($economy->inflation <=6){

        $recommendation='Monitor';

        $recommendationTitle='Monitor Economic Conditions';

        $recommendationColor='warning';

        $recommendationMessage='Inflation is increasing. Business decisions should be evaluated carefully before expansion.';

        $items=[

            'Review pricing strategy',

            'Monitor exchange rate',

            'Evaluate procurement costs',

            'Delay non-essential investments'

        ];

    }

    else{

        $recommendation='Protect';

        $recommendationTitle='Protect Business Stability';

        $recommendationColor='danger';

        $recommendationMessage='Economic pressure is high. Focus on protecting cash flow and reducing unnecessary risks.';

        $items=[

            'Reduce operational expenses',

            'Delay expansion projects',

            'Strengthen financial reserves',

            'Increase market monitoring'

        ];

    }

@endphp


<div class="row g-4 mb-4">

    {{-- Recommendation --}}
    <div class="col-lg-7">

        <div class="card dashboard-card h-100">

            <div class="card-body p-5">

                <div class="d-flex align-items-center mb-4">

                    <i class="bi bi-lightbulb-fill text-warning fs-2 me-3"></i>

                    <div>

                        <h2 class="fw-bold mb-1">

                            Business Recommendation

                        </h2>

                        <small class="text-muted">

                            Recommended strategy based on current economic indicators.

                        </small>

                    </div>

                </div>

                <div class="alert alert-{{ $recommendationColor }} border-0 rounded-4">

                    <h5 class="fw-bold mb-2">

                        {{ $recommendationTitle }}

                    </h5>

                    {{ $recommendationMessage }}

                </div>

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

    {{-- Economy Indicator --}}
    <div class="col-lg-5">

        <div class="card dashboard-card h-100">

            <div class="card-body p-5 text-center">

                <div class="mb-4">

                    <i class="bi bi-speedometer2 text-warning"
                       style="font-size:60px;"></i>

                </div>

                @php

                    $score=max(0,min(100,100-($economy->inflation*8)));

                @endphp

                <h2 class="fw-bold">

                    {{ $score }}%

                </h2>

                <p class="text-muted">

                    Economic Stability Score

                </p>

                <div class="progress rounded-pill mb-4"
                     style="height:14px;">

                    <div class="progress-bar bg-{{ $overviewColor }}"
                         style="width:{{ $score }}%">

                    </div>

                </div>

                <table class="table table-borderless align-middle mb-0">

                    <tr>

                        <td class="text-muted">

                            Economy

                        </td>

                        <td class="text-end">

                            <span class="badge bg-{{ $overviewColor }} rounded-pill">

                                {{ $overview }}

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Inflation

                        </td>

                        <td class="text-end">

                            {{ number_format($economy->inflation,2) }}%

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

                    Economic impact assessment for international business activities.

                </small>

            </div>

        </div>

        <div class="row g-4">

            {{-- Investment Climate --}}
            <div class="col-lg-3 col-md-6">

                <div class="impact-card h-100">

                    <div class="impact-icon bg-success-subtle">

                        <i class="bi bi-building-fill-check text-success"></i>

                    </div>

                    <h5 class="fw-bold mt-4">

                        Investment Climate

                    </h5>

                    <span class="badge bg-success rounded-pill">

                        Favorable

                    </span>

                    <p class="text-muted mt-3 mb-0">

                        Stable economic indicators continue to attract domestic and foreign investment.

                    </p>

                </div>

            </div>

            {{-- Export Opportunity --}}
            <div class="col-lg-3 col-md-6">

                <div class="impact-card h-100">

                    <div class="impact-icon bg-primary-subtle">

                        <i class="bi bi-box-arrow-up-right text-primary"></i>

                    </div>

                    <h5 class="fw-bold mt-4">

                        Export Opportunity

                    </h5>

                    <span class="badge bg-primary rounded-pill">

                        Strong

                    </span>

                    <p class="text-muted mt-3 mb-0">

                        Export performance remains positive with opportunities for market expansion.

                    </p>

                </div>

            </div>

            {{-- Import Cost --}}
            <div class="col-lg-3 col-md-6">

                <div class="impact-card h-100">

                    <div class="impact-icon bg-warning-subtle">

                        <i class="bi bi-cart-fill text-warning"></i>

                    </div>

                    <h5 class="fw-bold mt-4">

                        Import Cost

                    </h5>

                    <span class="badge bg-warning text-dark rounded-pill">

                        Moderate

                    </span>

                    <p class="text-muted mt-3 mb-0">

                        Import costs remain manageable but should be monitored regularly.

                    </p>

                </div>

            </div>

            {{-- Business Confidence --}}
            <div class="col-lg-3 col-md-6">

                <div class="impact-card h-100">

                    <div class="impact-icon bg-info-subtle">

                        <i class="bi bi-graph-up-arrow text-info"></i>

                    </div>

                    <h5 class="fw-bold mt-4">

                        Business Confidence

                    </h5>

                    <span class="badge bg-info rounded-pill">

                        Positive

                    </span>

                    <p class="text-muted mt-3 mb-0">

                        Overall business confidence remains optimistic under current macroeconomic conditions.

                    </p>

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

                    Executive summary and business insights.

                </small>

            </div>

        </div>

        <div class="row g-4">

            {{-- Economic Summary --}}
            <div class="col-lg-6">

                <div class="note-card h-100">

                    <div class="d-flex align-items-center mb-3">

                        <div class="note-icon bg-primary-subtle">

                            <i class="bi bi-file-earmark-text text-primary"></i>

                        </div>

                        <h5 class="fw-bold ms-3 mb-0">

                            Economic Summary

                        </h5>

                    </div>

                    <p class="text-muted mb-0">

                        {{ $overviewMessage }}

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

                            @case('Expand')

                                Current economic conditions support business expansion, export growth, and long-term investment opportunities.

                                @break

                            @case('Maintain')

                                Continue normal operations while monitoring inflation and trade performance.

                                @break

                            @case('Monitor')

                                Review major investments and closely monitor macroeconomic indicators before expansion.

                                @break

                            @default

                                Focus on financial stability, optimize operational costs, and postpone non-essential expansion.

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

                    Economic data recording history.

                </small>

            </div>

        </div>

        <div class="row g-4">

            {{-- Recorded At --}}
            <div class="col-lg-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-primary-subtle">

                        <i class="bi bi-calendar-check text-primary"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Data Year

                    </small>

                    <h5 class="fw-bold mt-2 mb-0">

                        {{ $economy->data_year }}

                    </h5>

                </div>

            </div>

            {{-- Last Updated --}}
            <div class="col-lg-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-success-subtle">

                        <i class="bi bi-arrow-repeat text-success"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Last Updated

                    </small>

                    <h5 class="fw-bold mt-2 mb-0">

                        {{ optional($economy->updated_at)->format('d M Y H:i') }}

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

                    Economy monitoring module information.

                </small>

            </div>

        </div>

        <div class="row g-4">

            {{-- Module --}}
            <div class="col-lg-3 col-md-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-warning-subtle">

                        <i class="bi bi-graph-up-arrow text-warning"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Module

                    </small>

                    <h6 class="fw-bold mt-2 mb-0">

                        Economy Monitoring

                    </h6>

                </div>

            </div>

            {{-- Data Source --}}
            <div class="col-lg-3 col-md-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-info-subtle">

                        <i class="bi bi-database-fill text-info"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Data Source

                    </small>

                    <h6 class="fw-bold mt-2 mb-0">

                        Economic Database

                    </h6>

                </div>

            </div>

            {{-- Generated --}}
            <div class="col-lg-3 col-md-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-success-subtle">

                        <i class="bi bi-calendar3 text-success"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Generated

                    </small>

                    <h6 class="fw-bold mt-2 mb-0">

                        {{ now()->format('d M Y') }}

                    </h6>

                </div>

            </div>

            {{-- Time --}}
            <div class="col-lg-3 col-md-6">

                <div class="info-card h-100">

                    <div class="info-icon bg-danger-subtle">

                        <i class="bi bi-clock-fill text-danger"></i>

                    </div>

                    <small class="text-muted d-block mt-3">

                        Time

                    </small>

                    <h6 class="fw-bold mt-2 mb-0">

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

                            Economy Monitoring Module

                        </div>

                        <small class="text-muted">

                            Version 1.0

                        </small>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                <a href="{{ route('economy.index') }}"
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

<style>

/* =========================================================
   HERO
========================================================= */

.hero-card{
    background:#fff;
    border-radius:22px;
    border:1px solid #edf2f7;
    padding:30px;
    margin-bottom:30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 8px 28px rgba(0,0,0,.06);
    transition:.3s;
}

.hero-card:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 35px rgba(0,0,0,.10);
}

.hero-country{
    display:flex;
    align-items:center;
    gap:25px;
}

.country-flag{
    width:82px;
    height:82px;
    object-fit:cover;
    border-radius:16px;
    border:1px solid #ececec;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.hero-title{
    font-size:42px;
    font-weight:700;
    margin-bottom:6px;
}

.hero-subtitle{
    color:#6c757d;
    font-size:18px;
    margin:0;
}

.hero-status{
    font-size:16px;
    padding:12px 28px;
    border-radius:50px;
    font-weight:700;
}


/* =========================================================
   PAGE HEADER
========================================================= */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.page-title{
    font-size:32px;
    font-weight:700;
    margin-bottom:6px;
}

.page-subtitle{
    color:#6c757d;
    margin-bottom:0;
}


/* =========================================================
   DASHBOARD CARD
========================================================= */

.dashboard-card{
    border:none;
    border-radius:22px;
    background:#fff;
    box-shadow:0 8px 28px rgba(0,0,0,.06);
    transition:.3s;
}

.dashboard-card:hover{
    transform:translateY(-4px);
    box-shadow:0 18px 38px rgba(0,0,0,.10);
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


/* =========================================================
   ANALYSIS CARD
========================================================= */

.analysis-card{
    border:1px solid #edf2f7;
    border-radius:18px;
    padding:25px;
    background:#fff;
    height:100%;
    transition:.25s;
}

.analysis-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 24px rgba(0,0,0,.08);
}


/* =========================================================
   IMPACT CARD
========================================================= */

.impact-card{
    background:#fff;
    border:1px solid #edf2f7;
    border-radius:18px;
    padding:25px;
    text-align:center;
    transition:.25s;
    height:100%;
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


/* =========================================================
   NOTE CARD
========================================================= */

.note-card{
    border:1px solid #edf2f7;
    border-radius:18px;
    padding:25px;
    background:#fff;
    transition:.25s;
    height:100%;
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


/* =========================================================
   INFO CARD
========================================================= */

.info-card{
    background:#fff;
    border:1px solid #edf2f7;
    border-radius:18px;
    padding:25px;
    text-align:center;
    transition:.25s;
    height:100%;
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
    justify-content:center;
    align-items:center;
    font-size:24px;
}


/* =========================================================
   TABLE
========================================================= */

.table td{
    padding:.85rem .5rem;
    vertical-align:middle;
}

.table thead th{
    background:#fff7e8;
    border:none;
    color:#555;
    font-weight:600;
}

.table-hover tbody tr{
    transition:.25s;
}

.table-hover tbody tr:hover{
    background:#fffaf2;
}


/* =========================================================
   BADGE
========================================================= */

.badge{
    font-weight:600;
    letter-spacing:.3px;
}


/* =========================================================
   BUTTON
========================================================= */

.btn{
    border-radius:50px;
    transition:.3s;
}

.btn:hover{
    transform:translateY(-2px);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width:992px){

    .hero-card{
        flex-direction:column;
        text-align:center;
        gap:25px;
    }

    .hero-country{
        flex-direction:column;
    }

    .hero-title{
        font-size:32px;
    }

    .page-header{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

}

</style>
@endsection