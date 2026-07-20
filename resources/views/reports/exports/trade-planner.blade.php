@extends('reports.exports.layout')

@section('title', 'Trade Planner Report')

@section('export_buttons')
    <a href="{{ route('trade-planner.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('trade-planner.excel') }}" class="btn btn-success">
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
                    <th>Origin Country</th>
                    <th>Destination Country</th>
                    <th>Origin Port</th>
                    <th>Destination Port</th>
                    <th>Cargo Type</th>
                    <th>Container Size</th>
                    <th>Distance</th>
                    <th>ETA</th>
                    <th>Weather Impact</th>
                    <th>Currency Impact</th>
                    <th>Trade Risk</th>
                    <th>AI Recommendation</th>
                    <th>Simulation Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row['origin_country'] ?? '-' }}</td>
                        <td>{{ $row['destination_country'] ?? '-' }}</td>
                        <td>{{ $row['origin_port'] ?? '-' }}</td>
                        <td>{{ $row['destination_port'] ?? '-' }}</td>
                        <td>{{ $row['cargo_type'] ?? '-' }}</td>
                        <td>{{ $row['container_size'] ?? '-' }}</td>
                        <td>{{ $row['distance'] ?? '-' }}</td>
                        <td>{{ $row['eta'] ?? '-' }}</td>
                        <td>{{ $row['weather_impact'] ?? '-' }}</td>
                        <td>{{ $row['currency_impact'] ?? '-' }}</td>
                        <td>
                            @php
                                $risk = strtolower($row['trade_risk'] ?? '');
                                $badgeClass = 'bg-secondary';
                                if(str_contains($risk, 'high')) $badgeClass = 'bg-danger';
                                elseif(str_contains($risk, 'medium')) $badgeClass = 'bg-warning';
                                elseif(str_contains($risk, 'low')) $badgeClass = 'bg-success';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['trade_risk'] ?? '-' }}</span>
                        </td>
                        <td>{{ $row['ai_recommendation'] ?? '-' }}</td>
                        <td>{{ $row['simulation_date'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
@endsection
