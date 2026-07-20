@extends('layouts.app')

@section('title', 'Weather Monitoring')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="mb-4">

        <h1 class="fw-bold display-5">
            🌦 Weather Monitoring
        </h1>

        <p class="text-muted fs-5">
            Monitor global weather conditions affecting international trade.
        </p>

    </div>

    {{-- Summary --}}
    <div class="row g-4 mb-4">

        <div class="col-xl-2 col-lg-4 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Total Countries
                    </div>

                    <h1 class="fw-bold mt-3">
                        {{ $totalCountries }}
                    </h1>

                </div>

            </div>

        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Average Temp
                    </div>

                    <h1 class="fw-bold mt-3 text-warning">

                        {{ number_format($averageTemperature,1) }}°

                    </h1>

                </div>

            </div>

        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Clear
                    </div>

                    <h1 class="fw-bold mt-3 text-success">

                        {{ $clearCount }}

                    </h1>

                </div>

            </div>

        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Rain
                    </div>

                    <h1 class="fw-bold mt-3 text-primary">

                        {{ $rainCount }}

                    </h1>

                </div>

            </div>

        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Storm
                    </div>

                    <h1 class="fw-bold mt-3 text-danger">

                        {{ $stormCount }}

                    </h1>

                </div>

            </div>

        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Extreme
                    </div>

                    <h1 class="fw-bold mt-3 text-dark">

                        {{ $extremeCount }}

                    </h1>

                </div>

            </div>

        </div>

    </div>

    {{-- Filter --}}
    <div class="card dashboard-card mb-4">

        <div class="card-body">

            <form method="GET" action="{{ route('weather.index') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-lg-3">

                        <label class="form-label fw-semibold">

                            Country

                        </label>

                        <select name="country" class="form-select">

                            <option value="All Countries" {{ request('country') == 'All Countries' ? 'selected' : '' }}>
                                All Countries
                            </option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ request('country') == $c->id ? 'selected' : '' }}>
                                    {{ $c->country_name }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-lg-3">

                        <label class="form-label fw-semibold">

                            Weather

                        </label>

                        <select name="condition" class="form-select">

                            <option value="All" {{ request('condition') == 'All' ? 'selected' : '' }}>All</option>
                            <option value="Clear" {{ request('condition') == 'Clear' ? 'selected' : '' }}>Clear</option>
                            <option value="Rain" {{ request('condition') == 'Rain' ? 'selected' : '' }}>Rain</option>
                            <option value="Storm" {{ request('condition') == 'Storm' ? 'selected' : '' }}>Storm</option>
                            <option value="Extreme" {{ request('condition') == 'Extreme' ? 'selected' : '' }}>Extreme</option>

                        </select>

                    </div>

                    <div class="col-lg-3">

                        <label class="form-label fw-semibold">

                            Storm Risk

                        </label>

                        <select name="storm_risk" class="form-select">

                            <option value="All" {{ request('storm_risk') == 'All' ? 'selected' : '' }}>All</option>
                            <option value="Low" {{ request('storm_risk') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ request('storm_risk') == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ request('storm_risk') == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Extreme" {{ request('storm_risk') == 'Extreme' ? 'selected' : '' }}>Extreme</option>

                        </select>

                    </div>

                    <div class="col-lg-3">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning w-100 py-2">
                                Filter
                            </button>
                            <a href="{{ route('weather.index') }}" class="btn btn-secondary w-100 py-2">
                                Reset
                            </a>
                        </div>

                    </div>

                </div>
            </form>

        </div>

    </div>

    {{-- Weather Overview --}}

<div class="row g-4 mb-4">

    <div class="col-lg-4">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5 class="fw-bold mb-4">

                    🌦 Trade Weather Overview

                </h5>

                <div class="mb-3">

                    <div class="text-muted">

                        Trade Status

                    </div>

                    <span class="badge bg-success fs-6">

                        {{ $tradeStatus }}

                    </span>

                </div>

                <div class="mb-3">

                    <div class="text-muted">

                        Average Storm Risk

                    </div>

                    <h3 class="fw-bold text-warning">

                        {{ number_format($averageStormRisk,1) }}

                    </h3>

                </div>

                <div>

                    <div class="text-muted">

                        Highest Storm Risk

                    </div>

                    <h3 class="fw-bold text-danger">

                        {{ $highestStormRisk }}

                    </h3>

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5 class="fw-bold mb-4">

                    📊 Weather Distribution

                </h5>

                <canvas id="weatherChart" height="260"></canvas>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5 class="fw-bold mb-4">

                    🌍 Global Weather Map

                </h5>

                <div id="weatherMap"
                     style="height:260px;border-radius:12px;"></div>

            </div>

        </div>

    </div>

</div>

<div class="card dashboard-card">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h4 class="fw-bold">

                🌍 Weather Monitoring

            </h4>

            <span class="badge bg-warning">

                {{ $weatherData->count() }}

                Records

            </span>

        </div>

        <div class="table-responsive">

            <table class="table align-middle table-hover">

                <thead>

                <tr>

                    <th>No</th>

                    <th>Country</th>

                    <th>Temperature</th>

                    <th>Rainfall</th>

                    <th>Wind Speed</th>

                    <th>Storm Risk</th>

                    <th>Status</th>

                    <th>Trade Impact</th>

                    <th>Updated</th>

                    <th></th>

                </tr>

                </thead>

                <tbody>

                @forelse($weatherData as $index => $weather)

                    @php

                        $badge='secondary';

                        switch($weather->weather_status){

                            case 'clear':
                                $badge='success';
                                break;

                            case 'rain':
                                $badge='primary';
                                break;

                            case 'storm':
                                $badge='warning';
                                break;

                            case 'extreme':
                                $badge='danger';
                                break;

                        }

                    @endphp

                    <tr>

                        <td>

                            {{ $index+1 }}

                        </td>

                        <td>

                            {{ $weather->country->country_name }}

                        </td>

                        <td>

                            {{ number_format($weather->temperature,1) }} °C

                        </td>

                        <td>

                            {{ number_format($weather->rainfall,1) }} mm

                        </td>

                        <td>

                            {{ number_format($weather->wind_speed,1) }} km/h

                        </td>

                        <td>

                            {{ number_format($weather->storm_risk,0) }}%

                        </td>

                        <td>

                            <span class="badge bg-{{ $badge }}">

                                {{ ucfirst($weather->weather_status) }}

                            </span>

                        </td>

                        <td>

                            @if($weather->storm_risk <=25)

                                <span class="badge bg-success">

                                    None

                                </span>

                            @elseif($weather->storm_risk<=50)

                                <span class="badge bg-primary">

                                    Low

                                </span>

                            @elseif($weather->storm_risk<=75)

                                <span class="badge bg-warning">

                                    Medium

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    High

                                </span>

                            @endif

                        </td>

                        <td>

                            {{ $weather->recorded_at->format('d M Y') }}

                        </td>

                        <td>
                            <a href="{{ route('weather.show',$weather->id) }}"
                            class="btn btn-warning btn-sm">

                                <i class="bi bi-eye"></i>

                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="10" class="text-center py-5">

                            No weather data found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@push('scripts')

<script>

const ctx = document.getElementById('weatherChart');

new Chart(ctx,{

    type:'doughnut',

    data:{

        labels:[
            'Clear',
            'Rain',
            'Storm',
            'Extreme'
        ],

        datasets:[{

            data:[
                {{ $clearCount }},
                {{ $rainCount }},
                {{ $stormCount }},
                {{ $extremeCount }}
            ],

            backgroundColor:[
                '#22c55e',
                '#3b82f6',
                '#f59e0b',
                '#ef4444'
            ]

        }]

    },

    options:{

        responsive:true,

        plugins:{
            legend:{
                position:'bottom'
            }
        }

    }

});

const map = L.map('weatherMap').setView([20,0],2);

L.tileLayer(

'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

{

maxZoom:18

}

).addTo(map);

const weatherData = @json($weatherData);

weatherData.forEach(function(item){

    if(!item.country) return;

    if(item.country.latitude == null || item.country.longitude == null) return;

    let color = "#22c55e";

    switch(item.weather_status){

        case "clear":
            color = "#22c55e";
            break;

        case "rain":
            color = "#2563eb";
            break;

        case "storm":
            color = "#f59e0b";
            break;

        case "extreme":
            color = "#ef4444";
            break;
    }

    L.circleMarker(
        [
            parseFloat(item.country.latitude),
            parseFloat(item.country.longitude)
        ],
        {
            radius:7,
            color:color,
            fillColor:color,
            fillOpacity:1,
            weight:2
        }
    )
    .bindPopup(`
        <strong>${item.country.country_name}</strong><br>
        Weather : ${item.weather_status}<br>
        Temp : ${item.temperature} °C<br>
        Storm Risk : ${item.storm_risk}%
    `)
    .addTo(map);

});
</script>

@endpush

@endsection