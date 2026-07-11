@extends('layouts.app')

@section('title','Dashboard')

@section('content')

{{-- ==========================================
Header
========================================== --}}

<div class="row mb-4">

    <div class="col-lg-8">

        <h2 class="fw-bold">

            Welcome 👋

        </h2>

        <p class="text-muted mb-0">

            Monitor global trade risks, weather, economy,
            logistics and international trade recommendations.

        </p>

    </div>

    <div class="col-lg-4 text-end">

        <span class="badge badge-orange p-3">

            Global Trade Dashboard

        </span>

    </div>

</div>

{{-- ==========================================
Summary Cards
========================================== --}}

@include('dashboard.components.summary-cards')

{{-- ==========================================
World Map
========================================== --}}

@include('dashboard.components.world-map')

<div class="row">

    <div class="col-lg-8">

        @include('dashboard.components.risk-chart')

    </div>

    <div class="row mt-4">

    <div class="col-lg-12">

        @include('dashboard.components.highest-risk')

    </div>

</div>

    <div class="row mt-4">

        <div class="col-12">

            @include('dashboard.components.recommendation-panel')

        </div>

    <div class="row mt-4">

        <div class="col-lg-6">

            @include('dashboard.components.weather-panel')

        </div>

        <div class="col-lg-6">

            @include('dashboard.components.currency-panel')

        </div>

    </div>

    <div class="col-lg-6">

        @include('dashboard.components.trade-insight')

    </div>

</div>

    <div class="col-lg-6">

        @include('dashboard.components.currency-panel')

    </div>

</div>

@include('dashboard.components.news-panel')

@endsection