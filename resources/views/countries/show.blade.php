@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="row">

        <div class="col-lg-12">

            @include('countries.components.header')

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-lg-8">

            @include('countries.components.analysis')

        </div>

        <div class="col-lg-4">

            @include('countries.components.recommendation')

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-lg-6">

            @include('countries.components.weather')

        </div>

        <div class="col-lg-6">

            @include('countries.components.currency')

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-lg-12">

            @include('countries.components.news')

        </div>

    </div>

</div>

@endsection
