@extends('layouts.app')

@section('title','Countries Monitoring')

@section('content')


<div class="container-fluid">


    {{-- Header --}}

    <div class="row mb-4">

        <div class="col-lg-8">

            <h2 class="fw-bold">

                🌍 Countries Monitoring

            </h2>

            <p class="text-muted">

                Explore 250 monitored countries with risk,
                weather, currency and trade information.

            </p>

        </div>

    </div>



    {{-- Search Filter --}}

    <div class="card border-0 shadow-sm rounded-4 mb-4">


        <div class="card-body">


            <form method="GET"
                  action="{{ route('countries.index') }}">


                <div class="row g-3">


                    <div class="col-lg-6">

                        <label class="small text-muted">

                            Search Country

                        </label>


                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control rounded-3"
                            placeholder="Search country name or code...">


                    </div>



                    <div class="col-lg-4">

                        <label class="small text-muted">

                            Region

                        </label>


                        <select
                            name="region"
                            class="form-select rounded-3">


                            <option value="">
                                All Regions
                            </option>


                            @foreach($regions as $region)

                                <option
                                    value="{{ $region }}"
                                    {{ request('region') == $region ? 'selected':'' }}>

                                    {{ $region }}

                                </option>

                            @endforeach


                        </select>


                    </div>



                    <div class="col-lg-2 d-flex align-items-end">


                        <button
                            class="btn btn-warning w-100 rounded-3">

                            Search

                        </button>


                    </div>


                </div>


            </form>


        </div>


    </div>




    {{-- Countries Card --}}


    <div class="row g-4">


        @forelse($countries as $country)


            <div class="col-xl-3 col-lg-4 col-md-6">


                <div class="card border-0 shadow-sm rounded-4 h-100">


                    <div class="card-body">


                        <div class="d-flex align-items-center mb-3">


                            <img
                                src="{{ $country->flag }}"
                                onerror="this.src='/images/default-flag.png'"
                                width="50"
                                class="rounded shadow-sm me-3">


                            <div>

                                <h6 class="fw-bold mb-0">

                                    {{ $country->country_name }}

                                </h6>


                                <small class="text-muted">

                                    {{ strtoupper($country->country_code) }}

                                </small>


                            </div>


                        </div>



                        <p class="small text-muted mb-2">

                            🌎 {{ $country->region }}

                        </p>



                        <p class="small mb-2">

                            💱
                            {{ $country->currency_code }}

                        </p>



                        <div class="mb-3">


                            @if($country->latestRisk)


                                @php

                                $level =
                                strtolower($country->latestRisk->risk_level);


                                $color = match($level){

                                    'critical'=>'danger',

                                    'dangerous'=>'warning',

                                    'alert'=>'warning',

                                    default=>'success'

                                };

                                @endphp



                                <span class="badge bg-{{ $color }}">

                                    {{ strtoupper($level) }}

                                </span>


                            @else

                                <span class="badge bg-secondary">

                                    No Risk Data

                                </span>

                            @endif


                        </div>



                        <a href="{{ route('countries.show',$country) }}"

                           class="btn btn-outline-warning w-100 rounded-3">


                            View Detail


                        </a>



                    </div>


                </div>


            </div>


        @empty


            <div class="text-center py-5">

                <h5>
                    Country not found
                </h5>

            </div>


        @endforelse


    </div>




    {{-- Pagination --}}

    <div class="d-flex justify-content-center mt-4">

    {{ $countries->links('pagination::bootstrap-5') }}

    </div>


</div>


@endsection