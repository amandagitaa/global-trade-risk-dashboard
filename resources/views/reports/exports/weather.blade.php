@extends('reports.exports.layout')

@section('title', 'Weather Report')

@section('export_buttons')
    <a href="{{ route('reports.weather.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('reports.weather.excel') }}" class="btn btn-success">
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
                    <th>Temperature</th>
                    <th>Humidity</th>
                    <th>Wind Speed</th>
                    <th>Weather Status</th>
                    <th>Storm Risk</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row['country'] ?? '-' }}</td>
                        <td>{{ $row['temperature'] ?? '-' }}</td>
                        <td>{{ $row['humidity'] ?? '-' }}</td>
                        <td>{{ $row['wind_speed'] ?? '-' }}</td>
                        <td>{{ $row['weather_status'] ?? '-' }}</td>
                        <td>
                            @php
                                $risk = strtolower($row['storm_risk'] ?? '');
                                $badgeClass = 'bg-secondary';
                                if(str_contains($risk, 'high')) $badgeClass = 'bg-danger';
                                elseif(str_contains($risk, 'medium')) $badgeClass = 'bg-warning';
                                elseif(str_contains($risk, 'low')) $badgeClass = 'bg-success';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['storm_risk'] ?? '-' }}</span>
                        </td>
                        <td>{{ $row['updated_at'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
@endsection
