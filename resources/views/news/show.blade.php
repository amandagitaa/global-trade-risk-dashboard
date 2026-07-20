@extends('layouts.app')

@section('content')

<div class="container-fluid">

{{-- ======================================================
    PAGE HEADER
======================================================= --}}

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">

            <i class="bi bi-newspaper text-warning me-2"></i>

            News Detail

        </h2>

        <p class="text-muted mb-0">

            Detailed analysis of international trade and supply chain news.

        </p>

    </div>

    <a href="{{ route('news.index') }}"
       class="btn btn-outline-secondary rounded-pill">

        <i class="bi bi-arrow-left me-2"></i>

        Back to News

    </a>

</div>

@php

    $badgeColor = match($news->sentiment){

        'positive' => 'success',

        'negative' => 'danger',

        default => 'warning'

    };

@endphp


{{-- ======================================================
    HERO CARD
======================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-5">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-warning bg-opacity-25
                                d-flex align-items-center justify-content-center
                                me-4"
                         style="width:80px;height:80px;">

                        <i class="bi bi-newspaper fs-2 text-warning"></i>

                    </div>

                    <div>

                        <span class="badge bg-{{ $badgeColor }} mb-3">

                            {{ ucfirst($news->sentiment) }}

                        </span>

                        <h2 class="fw-bold mb-3">

                            {{ $news->title }}

                        </h2>

                        <div class="text-muted">

                            <i class="bi bi-globe-americas me-2"></i>

                            {{ optional($news->country)->country_name ?? 'Unknown Country' }}

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="row g-3">

                    <div class="col-6">

                        <div class="border rounded-4 p-3 text-center">

                            <small class="text-muted">

                                Positive

                            </small>

                            <h3 class="fw-bold text-success mb-0">

                                {{ $news->positive_score }}

                            </h3>

                        </div>

                    </div>

                    <div class="col-6">

                        <div class="border rounded-4 p-3 text-center">

                            <small class="text-muted">

                                Negative

                            </small>

                            <h3 class="fw-bold text-danger mb-0">

                                {{ $news->negative_score }}

                            </h3>

                        </div>

                    </div>

                    <div class="col-12">

                        <div class="border rounded-4 p-3">

                            <small class="text-muted">

                                Published

                            </small>

                            <div class="fw-semibold mt-1">

                                {{ optional($news->published_at)->format('d M Y H:i') }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ======================================================
    NEWS INFORMATION
======================================================= --}}

<div class="row g-4 mb-4">

    {{-- News Information --}}
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="fw-bold mb-0">

                    <i class="bi bi-info-circle-fill text-primary me-2"></i>

                    News Information

                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <small class="text-muted d-block">

                            Source

                        </small>

                        <div class="fw-semibold fs-5">

                            {{ $news->source }}

                        </div>

                    </div>

                    <div class="col-md-6 mb-4">

                        <small class="text-muted d-block">

                            Category

                        </small>

                        <span class="badge bg-primary fs-6">

                            {{ ucfirst($news->category) }}

                        </span>

                    </div>

                    <div class="col-md-6 mb-4">

                        <small class="text-muted d-block">

                            Country

                        </small>

                        <div class="fw-semibold">

                            {{ optional($news->country)->country_name ?? 'Unknown Country' }}

                        </div>

                    </div>

                    <div class="col-md-6 mb-4">

                        <small class="text-muted d-block">

                            Published

                        </small>

                        <div class="fw-semibold">

                            {{ optional($news->published_at)->format('d F Y H:i') }}

                        </div>

                    </div>

                </div>

                @if($news->url)

                    <hr>

                    <a href="{{ $news->url }}"
                       target="_blank"
                       class="btn btn-primary">

                        <i class="bi bi-box-arrow-up-right me-2"></i>

                        Open Original Source

                    </a>

                @endif

            </div>

        </div>

    </div>

    {{-- Quick Statistics --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="fw-bold mb-0">

                    <i class="bi bi-bar-chart-fill text-success me-2"></i>

                    Quick Statistics

                </h5>

            </div>

            <div class="card-body">

                <div class="border rounded-4 p-3 mb-3 text-center">

                    <small class="text-muted">

                        Positive Score

                    </small>

                    <h2 class="text-success fw-bold mb-0">

                        {{ number_format($news->positive_score,2) }}

                    </h2>

                </div>

                <div class="border rounded-4 p-3 mb-3 text-center">

                    <small class="text-muted">

                        Negative Score

                    </small>

                    <h2 class="text-danger fw-bold mb-0">

                        {{ number_format($news->negative_score,2) }}

                    </h2>

                </div>

                <div class="border rounded-4 p-3 text-center">

                    <small class="text-muted">

                        Sentiment

                    </small>

                    <h4 class="mt-2">

                        <span class="badge bg-{{ $badgeColor }} px-3 py-2">

                            {{ ucfirst($news->sentiment) }}

                        </span>

                    </h4>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ======================================================
    NEWS SUMMARY
======================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold mb-0">

            <i class="bi bi-file-earmark-text-fill text-warning me-2"></i>

            News Summary

        </h5>

    </div>

    <div class="card-body">

        <p class="text-muted lh-lg mb-0">

            {{ $news->description }}

        </p>

    </div>

</div>
{{-- ======================================================
    TRADE RISK ANALYSIS
======================================================= --}}

@php

    if($news->sentiment == 'positive'){

        $riskLevel = 'Low Risk';
        $riskColor = 'success';
        $riskValue = 25;

        $recommendation =
        'Current conditions support international trade activities. Businesses may continue expansion while maintaining routine monitoring.';

    }elseif($news->sentiment == 'neutral'){

        $riskLevel = 'Medium Risk';
        $riskColor = 'warning';
        $riskValue = 55;

        $recommendation =
        'Market conditions remain relatively stable. Continue monitoring supply chain performance and prepare mitigation strategies.';

    }else{

        $riskLevel = 'High Risk';
        $riskColor = 'danger';
        $riskValue = 85;

        $recommendation =
        'Potential disruption detected. Review procurement plans, diversify suppliers, and strengthen inventory management.';

    }

@endphp

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0">

        <h4 class="fw-bold mb-0">

            <i class="bi bi-speedometer2 text-danger me-2"></i>

            Trade Risk Analysis

        </h4>

    </div>

    <div class="card-body">

        <div class="row">

            {{-- Risk Score --}}
            <div class="col-lg-4">

                <div class="border rounded-4 text-center p-4 h-100">

                    <small class="text-muted">

                        Trade Risk Score

                    </small>

                    <h1 class="display-4 fw-bold text-{{ $riskColor }} mt-2">

                        {{ $riskValue }}

                    </h1>

                    <span class="badge bg-{{ $riskColor }} px-3 py-2">

                        {{ $riskLevel }}

                    </span>

                </div>

            </div>

            {{-- Indicators --}}
            <div class="col-lg-4">

                <div class="border rounded-4 p-4 h-100">

                    <h5 class="fw-bold mb-3">

                        Risk Indicators

                    </h5>

                    <table class="table table-borderless mb-0">

                        <tr>

                            <td>News Sentiment</td>

                            <td class="text-end">

                                <span class="badge bg-{{ $badgeColor }}">

                                    {{ ucfirst($news->sentiment) }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <td>Positive Score</td>

                            <td class="text-end text-success fw-bold">

                                {{ $news->positive_score }}

                            </td>

                        </tr>

                        <tr>

                            <td>Negative Score</td>

                            <td class="text-end text-danger fw-bold">

                                {{ $news->negative_score }}

                            </td>

                        </tr>

                        <tr>

                            <td>Category</td>

                            <td class="text-end">

                                {{ ucfirst($news->category) }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            {{-- Recommendation --}}
            <div class="col-lg-4">

                <div class="border rounded-4 p-4 h-100">

                    <h5 class="fw-bold mb-3">

                        Business Recommendation

                    </h5>

                    <p class="text-muted mb-0">

                        {{ $recommendation }}

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ======================================================
    SUPPLY CHAIN IMPACT
======================================================= --}}

@php

    if($news->sentiment == 'positive'){

        $shipping = ['Normal','success'];
        $logistics = ['Efficient','success'];
        $manufacturing = ['Growing','primary'];
        $transportation = ['Stable','success'];
        $trade = ['Expanding','primary'];

    }elseif($news->sentiment == 'neutral'){

        $shipping = ['Monitor','warning'];
        $logistics = ['Normal','warning'];
        $manufacturing = ['Stable','warning'];
        $transportation = ['Watch','warning'];
        $trade = ['Balanced','warning'];

    }else{

        $shipping = ['Delayed','danger'];
        $logistics = ['High Risk','danger'];
        $manufacturing = ['Material Shortage','danger'];
        $transportation = ['Disrupted','danger'];
        $trade = ['Declining','danger'];

    }

@endphp

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0">

        <h4 class="fw-bold mb-0">

            <i class="bi bi-diagram-3-fill text-primary me-2"></i>

            Supply Chain Impact

        </h4>

    </div>

    <div class="card-body">

        <div class="row g-4">

            <div class="col-lg-4">

                <div class="border rounded-4 p-4 h-100 text-center">

                    <i class="bi bi-ship fs-1 text-primary"></i>

                    <h5 class="mt-3 fw-bold">

                        Shipping

                    </h5>

                    <span class="badge bg-{{ $shipping[1] }} px-3 py-2">

                        {{ $shipping[0] }}

                    </span>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="border rounded-4 p-4 h-100 text-center">

                    <i class="bi bi-box-seam fs-1 text-success"></i>

                    <h5 class="mt-3 fw-bold">

                        Logistics

                    </h5>

                    <span class="badge bg-{{ $logistics[1] }} px-3 py-2">

                        {{ $logistics[0] }}

                    </span>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="border rounded-4 p-4 h-100 text-center">

                    <i class="bi bi-building fs-1 text-warning"></i>

                    <h5 class="mt-3 fw-bold">

                        Manufacturing

                    </h5>

                    <span class="badge bg-{{ $manufacturing[1] }} px-3 py-2">

                        {{ $manufacturing[0] }}

                    </span>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="border rounded-4 p-4 h-100 text-center">

                    <i class="bi bi-truck fs-1 text-danger"></i>

                    <h5 class="mt-3 fw-bold">

                        Transportation

                    </h5>

                    <span class="badge bg-{{ $transportation[1] }} px-3 py-2">

                        {{ $transportation[0] }}

                    </span>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="border rounded-4 p-4 h-100 text-center">

                    <i class="bi bi-globe-americas fs-1 text-info"></i>

                    <h5 class="mt-3 fw-bold">

                        Import & Export

                    </h5>

                    <span class="badge bg-{{ $trade[1] }} px-3 py-2">

                        {{ $trade[0] }}

                    </span>

                </div>

            </div>

        </div>

        <hr class="my-5">

        <div class="row text-center">

            <div class="col-lg-3">

                <h3 class="fw-bold text-primary">

                    250+

                </h3>

                <small class="text-muted">

                    Monitored Countries

                </small>

            </div>

            <div class="col-lg-3">

                <h3 class="fw-bold text-success">

                    {{ $news->positive_score }}

                </h3>

                <small class="text-muted">

                    Positive Score

                </small>

            </div>

            <div class="col-lg-3">

                <h3 class="fw-bold text-danger">

                    {{ $news->negative_score }}

                </h3>

                <small class="text-muted">

                    Negative Score

                </small>

            </div>

            <div class="col-lg-3">

                <h3 class="fw-bold text-{{ $riskColor }}">

                    {{ $riskValue }}

                </h3>

                <small class="text-muted">

                    Risk Score

                </small>

            </div>

        </div>

    </div>

</div>

{{-- ======================================================
    NEWS SOURCE & SYSTEM ANALYSIS
======================================================= --}}

<div class="row g-4 mb-4">

    {{-- News Source --}}
    <div class="col-lg-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="fw-bold mb-0">

                    <i class="bi bi-broadcast text-primary me-2"></i>

                    News Source

                </h5>

            </div>

            <div class="card-body">

                <table class="table table-borderless align-middle">

                    <tr>

                        <td width="35%" class="text-muted">

                            Source

                        </td>

                        <td>

                            <strong>{{ $news->source }}</strong>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Country

                        </td>

                        <td>

                            {{ optional($news->country)->country_name ?? 'Unknown Country' }}

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Category

                        </td>

                        <td>

                            <span class="badge bg-primary">

                                {{ ucfirst($news->category) }}

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Published

                        </td>

                        <td>

                            {{ optional($news->published_at)->format('d F Y H:i') }}

                        </td>

                    </tr>

                </table>

                @if($news->url)

                    <hr>

                    <a href="{{ $news->url }}"
                       target="_blank"
                       class="btn btn-primary">

                        <i class="bi bi-box-arrow-up-right me-2"></i>

                        Visit Original Website

                    </a>

                @endif

            </div>

        </div>

    </div>

    {{-- System Analysis --}}
    <div class="col-lg-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="fw-bold mb-0">

                    <i class="bi bi-cpu-fill text-success me-2"></i>

                    AI Trade Analysis

                </h5>

            </div>

            <div class="card-body">

                <div class="alert alert-{{ $riskColor }} border-0">

                    <h5 class="fw-bold">

                        {{ $riskLevel }}

                    </h5>

                    <p class="mb-0">

                        {{ $recommendation }}

                    </p>

                </div>

                <div class="mt-4">

                    <div class="d-flex justify-content-between mb-3">

                        <span>News Sentiment</span>

                        <strong>{{ ucfirst($news->sentiment) }}</strong>

                    </div>

                    <div class="progress mb-4" style="height:10px;">

                        <div class="progress-bar bg-{{ $riskColor }}"
                             style="width:{{ $riskValue }}%">

                        </div>

                    </div>

                    <div class="row text-center">

                        <div class="col-4">

                            <h5 class="text-success fw-bold">

                                {{ $news->positive_score }}

                            </h5>

                            <small class="text-muted">

                                Positive

                            </small>

                        </div>

                        <div class="col-4">

                            <h5 class="text-danger fw-bold">

                                {{ $news->negative_score }}

                            </h5>

                            <small class="text-muted">

                                Negative

                            </small>

                        </div>

                        <div class="col-4">

                            <h5 class="text-{{ $riskColor }} fw-bold">

                                {{ $riskValue }}

                            </h5>

                            <small class="text-muted">

                                Risk

                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ======================================================
    SYSTEM INFORMATION
======================================================= --}}

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="row align-items-center">

            {{-- Left --}}
            <div class="col-lg-8">

                <div class="d-flex">

                    <div class="rounded-circle bg-warning bg-opacity-25
                                d-flex align-items-center justify-content-center me-4"
                         style="width:70px;height:70px;">

                        <i class="bi bi-shield-check fs-2 text-warning"></i>

                    </div>

                    <div>

                        <h4 class="fw-bold mb-2">

                            Global Trade Risk Intelligence Platform

                        </h4>

                        <p class="text-muted mb-2">

                            This news has been analyzed automatically using
                            sentiment analysis and is prepared for integration
                            with the Trade Risk Score module.

                        </p>

                        <span class="badge bg-success">

                            Monitoring Active

                        </span>

                    </div>

                </div>

            </div>

            {{-- Right --}}
            <div class="col-lg-4">

                <table class="table table-borderless table-sm mb-0">

                    <tr>

                        <td class="text-muted">

                            News ID

                        </td>

                        <td class="text-end fw-semibold">

                            #{{ $news->id }}

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Last Updated

                        </td>

                        <td class="text-end">

                            {{ optional($news->updated_at)->format('d M Y H:i') }}

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Status

                        </td>

                        <td class="text-end">

                            <span class="badge bg-success">

                                Active

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">

                            Platform

                        </td>

                        <td class="text-end">

                            v1.0

                        </td>

                    </tr>

                </table>

            </div>

        </div>

        <hr>

        <div class="row">

            <div class="col-md-6">

                <small class="text-muted">

                    © {{ date('Y') }}
                    Global Trade Risk Intelligence Platform.
                    All Rights Reserved.

                </small>

            </div>

            <div class="col-md-6 text-md-end">

                <small class="text-muted">

                    Powered by Laravel · Bootstrap · News API · World Bank API

                </small>

            </div>

        </div>

    </div>

</div>

</div>
@endsection