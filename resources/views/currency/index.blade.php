@extends('layouts.app')

@section('title', 'Currency Monitoring')

@section('content')

<div class="container-fluid">

    {{-- ===========================
        HEADER
    ============================ --}}
    <div class="mb-4">

        <h1 class="fw-bold">
            💱 Currency Monitoring
        </h1>

        <p class="text-muted">
            Monitor global currency exchange rates affecting international trade.
        </p>

    </div>



    {{-- ===========================
        SUMMARY
    ============================ --}}
    <div class="row g-4 mb-4">

        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <small class="text-muted">
                        Total Currency Data
                    </small>

                    <h2 class="fw-bold mt-2">
                        {{ $totalCurrencies }}
                    </h2>

                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <small class="text-muted">
                        Average Rate
                    </small>

                    <h2 class="fw-bold text-warning mt-2">
                        {{ number_format($averageRate,2) }}
                    </h2>

                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <small class="text-muted">
                        Highest Rate
                    </small>

                    <h2 class="fw-bold text-success mt-2">
                        {{ number_format($highestRate,2) }}
                    </h2>

                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <small class="text-muted">
                        Lowest Rate
                    </small>

                    <h2 class="fw-bold text-danger mt-2">
                        {{ number_format($lowestRate,2) }}
                    </h2>

                </div>
            </div>
        </div>

    </div>



    {{-- ===========================
        FILTER
    ============================ --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row">

                    <div class="col-md-5">

                        <label class="form-label">
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
                                    @selected(request('country')==$country->id)>

                                    {{ $country->country_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-5">

                        <label class="form-label">
                            Currency
                        </label>

                        <select
                            name="currency"
                            class="form-select">

                            <option value="">
                                All Currency
                            </option>

                            @foreach($currencyList as $currency)

                                <option
                                    value="{{ $currency }}"
                                    @selected(request('currency')==$currency)>

                                    {{ $currency }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2 d-grid">

                        <label class="form-label text-white">
                            Filter
                        </label>

                        <button
                            class="btn btn-warning">

                            Filter

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>



    {{-- ===========================
        TABLE
    ============================ --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">

            <h5 class="mb-0">

                Currency Exchange Rates

            </h5>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>

                    <tr>

                        <th>Country</th>
                        <th>Base</th>
                        <th>Target</th>
                        <th>Rate</th>
                        <th>Change</th>
                        <th>Recorded</th>
                        <th width="120">Action</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($currencies as $currency)

                    <tr>

                        <td>

                            {{ optional($currency->country)->country_name ?? '-' }}

                        </td>

                        <td>

                            {{ $currency->base_currency }}

                        </td>

                        <td>

                            {{ $currency->target_currency }}

                        </td>

                        <td>

                            {{ number_format($currency->exchange_rate,2) }}

                        </td>

                        <td>

                            {{ number_format($currency->change_percentage, 2) }} %

                        </td>

                        <td>

                            {{ optional($currency->recorded_at)->format('d M Y H:i') }}

                        </td>

                        <td>

                            <a
                                href="{{ route('currency.show',$currency) }}"
                                class="btn btn-warning btn-sm">

                                Detail

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center p-5">

                            No currency data found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <div class="mt-4">

        {{ $currencies->links() }}

    </div>

</div>

@endsection