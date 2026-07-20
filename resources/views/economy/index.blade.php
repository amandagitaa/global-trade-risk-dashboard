@extends('layouts.app')

@section('title', 'Economy Monitoring')

@section('content')

<div class="container-fluid">

    {{-- ==========================================
        PAGE HEADER
    ========================================== --}}
    <div class="mb-4">

        <h1 class="fw-bold display-5 d-flex align-items-center">

            <i class="bi bi-graph-up-arrow text-warning me-3"></i>

            Economy Monitoring

        </h1>

        <p class="text-muted fs-5 mb-0">

            Monitor macroeconomic indicators affecting international trade and global supply chains.

        </p>

    </div>


    {{-- ==========================================
        SUMMARY CARDS
    ========================================== --}}
    <div class="row g-4 mb-4">

        {{-- Total Records --}}
        <div class="col-lg-3 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body p-4">

                    <small class="text-muted d-block mb-2">

                        Total Economic Records

                    </small>

                    <h2 class="fw-bold text-primary mb-0">

                        {{ number_format($statistics['total']) }}

                    </h2>

                </div>

            </div>

        </div>

        {{-- Average GDP --}}
        <div class="col-lg-3 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body p-4">

                    <small class="text-muted d-block mb-2">

                        Average GDP

                    </small>

                    <h2 class="fw-bold text-success mb-0">

                        $

                        {{ number_format($statistics['avg_gdp'] / 1000000000, 2) }}

                        B

                    </h2>

                </div>

            </div>

        </div>

        {{-- Highest GDP --}}
        <div class="col-lg-3 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body p-4">

                    <small class="text-muted d-block mb-2">

                        Highest GDP

                    </small>

                    <h2 class="fw-bold text-warning mb-0">

                        $

                        {{ number_format($statistics['highest_gdp'] / 1000000000, 2) }}

                        B

                    </h2>

                </div>

            </div>

        </div>

        {{-- Average Inflation --}}
        <div class="col-lg-3 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body p-4">

                    <small class="text-muted d-block mb-2">

                        Average Inflation

                    </small>

                    <h2 class="fw-bold text-danger mb-0">

                        {{ number_format($statistics['avg_inflation'],2) }}%

                    </h2>

                </div>

            </div>

        </div>

    </div>

        {{-- ==========================================
        FILTER & ACTION
    ========================================== --}}
    <div class="card dashboard-card mb-4">

        <div class="card-body p-4">

            <form method="GET" action="{{ route('economy.index') }}">

                <div class="row g-3 align-items-end">

                    {{-- Country --}}
                    <div class="col-lg-5">

                        <label class="form-label fw-semibold">

                            Country

                        </label>

                        <select
                            name="country"
                            class="form-select">

                            <option value="">

                                All Countries

                            </option>

                            @foreach($countries as $country)

                                <option
                                    value="{{ $country->id }}"
                                    {{ request('country') == $country->id ? 'selected' : '' }}>

                                    {{ $country->country_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Year --}}
                    <div class="col-lg-4">

                        <label class="form-label fw-semibold">

                            Year

                        </label>

                        <select
                            name="year"
                            class="form-select">

                            <option value="">

                                All Years

                            </option>

                            @for($year = date('Y'); $year >= 2015; $year--)

                                <option
                                    value="{{ $year }}"
                                    {{ request('year') == $year ? 'selected' : '' }}>

                                    {{ $year }}

                                </option>

                            @endfor

                        </select>

                    </div>

                    {{-- Filter & Reset --}}
                    <div class="col-lg-3">

                        <div class="d-flex gap-2">
                            <button
                                type="submit"
                                class="btn btn-warning w-100 py-2">
                                <i class="bi bi-funnel-fill"></i> Filter
                            </button>

                            <a href="{{ route('economy.index') }}"
                               class="btn btn-outline-secondary w-100 py-2">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

        {{-- ==========================================
        ECONOMIC INDICATORS TABLE
    ========================================== --}}
    <div class="card dashboard-card">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

                <div>

                    <h4 class="fw-bold mb-1">

                        <i class="bi bi-table text-warning me-2"></i>

                        Economic Indicators

                    </h4>

                    <small class="text-muted">

                        Latest macroeconomic indicators by country

                    </small>

                </div>

                <span class="badge bg-warning text-dark fs-6 px-3 py-2">

                    {{ $economies->total() }}

                    Records

                </span>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th width="60">#</th>

                            <th>Country</th>

                            <th>GDP</th>

                            <th>Inflation</th>

                            <th>Exports</th>

                            <th>Imports</th>

                            <th>Year</th>

                            <th>Status</th>

                            <th width="90" class="text-center">

                                Action

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($economies as $index => $economy)

                            @php

                                if($economy->inflation <= 2){

                                    $badge='success';

                                    $status='Stable';

                                }

                                elseif($economy->inflation <=5){

                                    $badge='warning';

                                    $status='Moderate';

                                }

                                else{

                                    $badge='danger';

                                    $status='High';

                                }

                            @endphp

                            <tr>

                                <td>

                                    {{ $economies->firstItem() + $index }}

                                </td>

                                <td>

                                    <div class="d-flex align-items-center">

                                        <img
                                            src="{{ $economy->country->flag }}"
                                            alt="{{ $economy->country->country_name }}"
                                            class="rounded shadow-sm me-2"
                                            style="width:34px;height:24px;object-fit:cover;">

                                        <strong>

                                            {{ $economy->country->country_name }}

                                        </strong>

                                    </div>

                                </td>

                                <td>

                                    {{ $economy->formatted_gdp }}

                                </td>

                                <td>

                                    <span class="badge bg-{{ $economy->inflation_color }}">

                                        {{ number_format($economy->inflation,2) }}%

                                    </span>

                                </td>

                                <td>

                                    {{ $economy->formatted_exports }}

                                </td>

                                <td>

                                    {{ $economy->formatted_imports }}

                                </td>

                                <td>

                                    {{ $economy->data_year }}

                                </td>

                                <td>

                                    <span class="badge bg-{{ $badge }} {{ $badge=='warning' ? 'text-dark' : '' }}">

                                        {{ $status }}

                                    </span>

                                </td>

                                <td class="text-center">

                                    <a
                                        href="{{ route('economy.show',$economy) }}"
                                        class="btn btn-warning btn-sm rounded-pill">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9">

                                    <div class="text-center py-5">

                                        <i class="bi bi-database-x display-5 text-secondary"></i>

                                        <h5 class="mt-3">

                                            No Economic Data Found

                                        </h5>

                                        <p class="text-muted mb-0">

                                            There is currently no economic data available.

                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="d-flex justify-content-end mt-4">

                {{ $economies->links() }}

            </div>

        </div>

    </div>
    @push('styles')

<style>

/* ==========================================
   DASHBOARD CARD
========================================== */

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


/* ==========================================
   PAGE HEADER
========================================== */

.page-title{

    font-size:2.4rem;

    font-weight:700;

}

.page-subtitle{

    font-size:1rem;

    color:#6c757d;

}


/* ==========================================
   SUMMARY CARD
========================================== */

.dashboard-card h2{

    font-weight:700;

    margin-bottom:0;

}

.dashboard-card small{

    color:#6c757d;

}


/* ==========================================
   FILTER
========================================== */

.form-label{

    font-weight:600;

}

.form-select,
.form-control{

    border-radius:12px;

    min-height:46px;

}


/* ==========================================
   TABLE
========================================== */

.table{

    margin-bottom:0;

}

.table thead th{

    background:#fff7e8;

    border:none;

    color:#555;

    font-weight:600;

    white-space:nowrap;

}

.table td{

    vertical-align:middle;

    padding:.95rem .75rem;

}

.table-hover tbody tr{

    transition:.25s;

}

.table-hover tbody tr:hover{

    background:#fffaf2;

}


/* ==========================================
   BADGE
========================================== */

.badge{

    font-weight:600;

    letter-spacing:.3px;

}


/* ==========================================
   BUTTON
========================================== */

.btn{

    border-radius:12px;

}

.btn-warning{

    color:#fff;

}


/* ==========================================
   FLAG
========================================== */

.flag-icon{

    width:34px;

    height:24px;

    object-fit:cover;

    border-radius:6px;

    box-shadow:0 2px 6px rgba(0,0,0,.12);

}


/* ==========================================
   PAGINATION
========================================== */

.pagination{

    margin-bottom:0;

}


/* ==========================================
   EMPTY STATE
========================================== */

.empty-state{

    padding:60px 20px;

}

.empty-state i{

    font-size:56px;

    color:#adb5bd;

}


/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width:992px){

    .page-title{

        font-size:2rem;

    }

    .table{

        font-size:.95rem;

    }

}

</style>

@endpush
@endsection