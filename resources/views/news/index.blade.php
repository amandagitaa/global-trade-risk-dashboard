@extends('layouts.app')


@section('content')

<div class="container-fluid">


{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold">
            Global News
        </h1>

        <p class="text-muted">
            Monitor international trade, logistics, economy,
            shipping and geopolitical developments.
        </p>
    </div>


    <a href="{{ route('news.sync') }}"
       class="btn btn-warning px-4">

        🔄 Sync News

    </a>

</div>



{{-- STATISTIC CARDS --}}

<div class="row g-4 mb-4">


<div class="col-md-3">

<div class="card shadow-sm border-0">

<div class="card-body">

<h6>Total News</h6>

<h2 class="fw-bold">
{{ number_format($totalNews) }}
</h2>

<span class="text-success">
Latest Articles
</span>

</div>

</div>

</div>




<div class="col-md-3">

<div class="card shadow-sm border-0">

<div class="card-body">

<h6>Positive News</h6>

<h2 class="text-success fw-bold">
{{ $positiveNews }}
</h2>


<span>
{{ $totalNews > 0 ? round(($positiveNews/$totalNews)*100) : 0 }}%
Positive
</span>


</div>

</div>

</div>




<div class="col-md-3">

<div class="card shadow-sm border-0">

<div class="card-body">

<h6>Neutral News</h6>

<h2 class="text-warning fw-bold">
{{ $neutralNews }}
</h2>


<span>
{{ $totalNews > 0 ? round(($neutralNews/$totalNews)*100) : 0 }}%
Neutral
</span>


</div>

</div>

</div>




<div class="col-md-3">

<div class="card shadow-sm border-0">

<div class="card-body">

<h6>Negative News</h6>

<h2 class="text-danger fw-bold">
{{ $negativeNews }}
</h2>


<span>
{{ $totalNews > 0 ? round(($negativeNews/$totalNews)*100) : 0 }}%
Negative
</span>


</div>

</div>

</div>


</div>

{{-- FILTER AREA --}}

<div class="card shadow-sm border-0 mb-4">

<div class="card-body">


<form method="GET" action="{{ route('news.index') }}">


<div class="row g-3">


{{-- SEARCH --}}

<div class="col-md-4">

<label class="form-label">
Search News / Country
</label>


<input type="text"
       name="search"
       class="form-control"
       placeholder="Search country, title, source..."
       value="{{ request('search') }}">


</div>



{{-- COUNTRY --}}

<div class="col-md-3">

<label class="form-label">
Country
</label>


<select name="country"
        class="form-select">


<option value="">
All Countries
</option>


@foreach($countries as $country)


<option value="{{ $country->id }}"
@if(request('country') == $country->id)
selected
@endif
>

{{ $country->country_name }}

</option>


@endforeach


</select>


</div>




{{-- CATEGORY --}}

<div class="col-md-2">

<label class="form-label">
Category
</label>


<select name="category"
        class="form-select">


<option value="">
All
</option>


@foreach($categories as $category)


<option value="{{ $category }}"
@if(request('category') == $category)
selected
@endif
>

{{ ucfirst($category) }}

</option>


@endforeach


</select>


</div>




{{-- SENTIMENT --}}

<div class="col-md-2">

<label class="form-label">
Sentiment
</label>


<select name="sentiment"
        class="form-select">


<option value="">
All
</option>


<option value="positive"
@if(request('sentiment')=='positive')
selected
@endif
>
Positive
</option>


<option value="neutral"
@if(request('sentiment')=='neutral')
selected
@endif
>
Neutral
</option>


<option value="negative"
@if(request('sentiment')=='negative')
selected
@endif
>
Negative
</option>


</select>


</div>




<div class="col-md-1 d-flex align-items-end">


<button class="btn btn-primary w-100">

🔍

</button>


</div>


</div>


</form>


</div>

</div>





{{-- NEWS GRID --}}


<div class="row g-4">


@forelse($news as $item)



<div class="col-xl-4 col-lg-6">



<div class="card h-100 shadow-sm border-0 overflow-hidden">



{{-- IMAGE --}}

@if($item->image_url)

<img src="{{ asset($item->image_url) }}"
     class="card-img-top"
     style="height:220px;object-fit:cover">

@endif




<div class="card-body">



<div class="d-flex justify-content-between mb-3">


<span class="badge bg-primary">

{{ strtoupper($item->category) }}

</span>



@if($item->sentiment == 'positive')


<span class="badge bg-success">
Positive
</span>



@elseif($item->sentiment == 'negative')


<span class="badge bg-danger">
Negative
</span>



@else


<span class="badge bg-warning">
Neutral
</span>



@endif



</div>




<h5 class="fw-bold mb-1">

{{ $item->title }}

</h5>

<div class="mb-2 text-muted small">
    <i class="bi bi-clock"></i> {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d M Y, H:i') : ($item->created_at ? $item->created_at->format('d M Y, H:i') : 'Unknown Date') }}
</div>

<p class="text-muted">

{{ Str::limit($item->description,120) }}

</p>





{{-- COUNTRY --}}

<div class="mb-2">


<strong>
Country:
</strong>


{{ $item->country->country_name ?? 'Unknown' }}


</div>





{{-- SOURCE --}}

<div class="mb-3">


<strong>
Source:
</strong>


{{ $item->source }}


</div>





{{-- RISK --}}


<div class="mb-3">


<strong>
Trade Risk:
</strong>


<span class="
badge

@if($item->risk_level=='High')
bg-danger

@elseif($item->risk_level=='Medium')
bg-warning

@else
bg-success

@endif

">


{{ $item->risk_level }}


</span>


</div>

{{-- TRADE IMPACT --}}

@if($item->trade_impact)

<div class="mb-3">

<strong>
Trade Impact
</strong>


<div class="mt-2">


@php

$impact = json_decode(
    $item->trade_impact,
    true
);

@endphp



@if(isset($impact['shipping']))

<span class="badge bg-light text-dark me-1">

🚢 Shipping:
{{ $impact['shipping'] }}

</span>

@endif



@if(isset($impact['logistics']))

<span class="badge bg-light text-dark me-1">

📦 Logistics:
{{ $impact['logistics'] }}

</span>

@endif



@if(isset($impact['export']))

<span class="badge bg-light text-dark me-1">

🌍 Export:
{{ $impact['export'] }}

</span>

@endif



</div>

</div>

@endif





{{-- QUALITY --}}

@if($item->verification_status)

<div class="mb-3">


<span class="badge bg-dark">

✓ {{ $item->verification_status }}

</span>


<span class="badge bg-secondary">

Score:
{{ $item->quality_score }}

</span>


</div>

@endif





{{-- BUTTON --}}


<div class="d-flex gap-2">


<a href="{{ route('news.show', $item) }}"
   class="btn btn-primary btn-sm">
    View Analysis
</a>



@if($item->original_url)
<a href="{{ $item->original_url }}"
   target="_blank"
   rel="noopener noreferrer"
   class="btn btn-outline-primary btn-sm">
    Read Original Source
</a>
@endif



</div>



</div>


</div>


</div>


@empty


<div class="col-12">


<div class="alert alert-warning text-center">

No news found.

</div>


</div>


@endforelse


</div>





{{-- PAGINATION --}}


<div class="mt-5">


{{ $news->links() }}


</div>



</div>


@endsection