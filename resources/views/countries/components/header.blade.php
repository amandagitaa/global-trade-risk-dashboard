<div class="card border-0 shadow-sm rounded-4">

<div class="card-body p-4">


@php

$riskColor = match($risk?->risk_level){

    'safe' => 'success',
    'stable' => 'primary',
    'alert' => 'warning',
    'dangerous' => 'warning',
    'critical' => 'danger',
    default => 'secondary'

};

@endphp



<div class="row align-items-center">


{{-- Country --}}

<div class="col-lg-8">

<div class="d-flex align-items-center">


<img 
src="{{ $country->flag }}"
onerror="this.src='/images/default-flag.png'"
width="75"
class="rounded shadow-sm me-4">


<div>

<h2 class="fw-bold mb-1">

{{ strtoupper($country->country_name) }}

</h2>


<div class="text-muted">

{{ $country->region }}

•

{{ $country->capital }}

•

{{ $country->currency_code }}

</div>


</div>


</div>

</div>



{{-- Risk --}}

<div class="col-lg-4 text-end">


<span class="badge bg-{{ $riskColor }} px-4 py-2">

{{ strtoupper($risk?->risk_level ?? 'UNKNOWN') }}

</span>


<h2 class="fw-bold text-orange mt-3 mb-0">

{{ number_format($risk?->final_score ?? 0,2) }}

</h2>


<small class="text-muted">

Global Trade Risk Score

</small>

<form action="{{ route('watch-list.store') }}" method="POST" class="mt-3">
    @csrf
    <input type="hidden" name="watch_type" value="country">
    <input type="hidden" name="country_id" value="{{ $country->id }}">
    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-star-fill me-1"></i> Add to Watch List
    </button>
</form>

</div>


</div>



<hr class="my-4">



{{-- Economic Summary --}}

<div class="row text-center g-3">



<div class="col-md-3">

<div class="p-3 rounded-3 bg-light">


<small class="text-muted d-block">

Population

</small>


<strong class="fs-5">
{{ number_format(($country->population ?? 0) / 1000000, 1) }} M
</strong>


</div>

</div>


<div class="col-md-3">

    <div class="p-3 rounded-3 bg-light">

        <small class="text-muted d-block">
            GDP
        </small>

        <strong class="fs-5">
            {{ $economic?->formatted_gdp ?? '-' }}
        </strong>

    </div>

</div>

<div class="col-md-3">

<div class="p-3 rounded-3 bg-light">


<small class="text-muted d-block">

Currency

</small>


<strong class="fs-5">


{{ $country->currency_code }}


</strong>


</div>

</div>





<div class="col-md-3">

<div class="p-3 rounded-3 bg-light">


<small class="text-muted d-block">

Latest Weather

</small>


<strong class="fs-5">


@if($weather)

    {{ $weather->weather_icon }}

    {{ number_format($weather->temperature,1) }}°C

@else

    -

@endif


</strong>


</div>

</div>



</div>



</div>

</div>