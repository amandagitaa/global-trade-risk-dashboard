@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="mb-4">
        <h1 class="fw-bold">
            📊 Risk Analysis
        </h1>

        <p class="text-muted">
            Analyze global trade risks from all monitored countries.
        </p>
    </div>

    {{-- Summary --}}
    <div class="row g-4 mb-4">

        <div class="col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted">Total Countries</small>
                    <h2 class="fw-bold mt-2">
                        {{ $summary['total'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted">Average Risk</small>
                    <h2 class="fw-bold text-warning mt-2">
                        {{ $summary['average'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted">Safe</small>
                    <h2 class="fw-bold text-success mt-2">
                        {{ $summary['safe'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted">Stable</small>
                    <h2 class="fw-bold text-primary mt-2">
                        {{ $summary['stable'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted">Alert</small>
                    <h2 class="fw-bold text-warning mt-2">
                        {{ $summary['alert'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted">Dangerous</small>
                    <h2 class="fw-bold text-orange mt-2">
                        {{ $summary['dangerous'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted">Critical</small>
                    <h2 class="fw-bold text-danger mt-2">
                        {{ $summary['critical'] }}
                    </h2>
                </div>
            </div>
        </div>

    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label">Country</label>

                        <input
                            type="text"
                            class="form-control"
                            name="country"
                            value="{{ request('country') }}"
                            placeholder="Search country...">
                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Risk Level
                        </label>

                        <select class="form-select" name="level">

                            <option value="">All</option>

                            <option value="safe">Safe</option>
                            <option value="stable">Stable</option>
                            <option value="alert">Alert</option>
                            <option value="dangerous">Dangerous</option>
                            <option value="critical">Critical</option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Sort
                        </label>

                        <select class="form-select" name="sort">

                            <option value="">Highest Risk</option>
                            <option value="lowest">Lowest Risk</option>
                            <option value="country">Country Name</option>

                        </select>

                    </div>

                    <div class="col-md-2 d-grid">

                        <button class="btn btn-warning">
                            Filter
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- Ranking --}}
    <div class="card border-0 shadow rounded-4">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                🌍 Trade Risk Ranking
            </h5>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>

                <tr>

                    <th>No</th>
                    <th>Country</th>
                    <th>Weather</th>
                    <th>Currency</th>
                    <th>Economy</th>
                    <th>Port</th>
                    <th>News</th>
                    <th>Score</th>
                    <th>Level</th>
                    <th></th>

                </tr>

                </thead>

                <tbody>

                @foreach($riskScores as $risk)

                    <tr>

                        <td>{{ $riskScores->firstItem() + $loop->index }}</td>

                        <td>{{ $risk->country?->country_name }}</td>

                        <td>{{ $risk->weather_score }}</td>

                        <td>{{ $risk->currency_score }}</td>

                        <td>{{ $risk->economic_score }}</td>

                        <td>{{ $risk->port_score }}</td>

                        <td>{{ $risk->news_score }}</td>

                        <td>
                            <strong>{{ $risk->final_score }}</strong>
                        </td>

                        <td>

                            <span class="badge bg-primary">

                                {{ strtoupper($risk->risk_level) }}

                            </span>

                        </td>

                        <td>

                            @if($risk->country)

                                <a
                                    href="{{ route('countries.show',$risk->country->id) }}"
                                    class="btn btn-sm btn-outline-warning">

                                    Detail

                                </a>

                            @else

                                -

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

        <div class="card-footer bg-white">

    <div class="d-flex justify-content-end">

        {{ $riskScores->withQueryString()->links() }}

    </div>

</div>
    </div>

</div>

@endsection