@extends('reports.exports.layout')

@section('title', 'Countries Report')

@section('export_buttons')
    <a href="{{ route('reports.countries.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('reports.countries.excel') }}" class="btn btn-success">
        Export Excel
    </a>
@endsection

@section('content')
    
    @if(empty($data) || count($data) == 0)
        <div style="text-align: center; padding: 40px; color: #666; font-style: italic;">
            No data available
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Capital</th>
                    <th>Region</th>
                    <th>Currency</th>
                    <th>Population</th>
                    <th>GDP</th>
                    <th>Inflation</th>
                    <th>Export Value</th>
                    <th>Import Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row['country'] ?? '-' }}</td>
                        <td>{{ $row['capital'] ?? '-' }}</td>
                        <td>{{ $row['region'] ?? '-' }}</td>
                        <td>{{ $row['currency'] ?? '-' }}</td>
                        <td>{{ $row['population'] ?? '-' }}</td>
                        <td>{{ $row['gdp'] ?? '-' }}</td>
                        <td>{{ $row['inflation'] ?? '-' }}</td>
                        <td>{{ $row['export_value'] ?? '-' }}</td>
                        <td>{{ $row['import_value'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
@endsection
