@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow">

        <div class="card-header">

            <h4>Countries</h4>

        </div>

        <div class="card-body">

            <table class="table table-striped">

                <thead>

                <tr>

                    <th>Flag</th>

                    <th>Country</th>

                    <th>Region</th>

                    <th>Currency</th>

                    <th></th>

                </tr>

                </thead>

                <tbody>

                @foreach($countries as $country)

                <tr>

                    <td>

                        <img
                            src="{{ $country->flag }}"
                            width="35">

                    </td>

                    <td>

                        {{ $country->country_name }}

                    </td>

                    <td>

                        {{ $country->region }}

                    </td>

                    <td>

                        {{ $country->currency_code }}

                    </td>

                    <td>

                        <a
                            href="{{ route('countries.show',$country) }}"
                            class="btn btn-primary btn-sm">

                            Detail

                        </a>

                    </td>

                </tr>

                @endforeach

                </tbody>

            </table>

            {{ $countries->links() }}

        </div>

    </div>

</div>

@endsection