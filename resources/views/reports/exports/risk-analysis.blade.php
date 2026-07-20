@extends('reports.exports.layout')

@section('title', 'Risk Analysis Report')

@section('export_buttons')
    <a href="{{ route('risk-analysis.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('risk-analysis.excel') }}" class="btn btn-success">
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
                    <th>Risk Score</th>
                    <th>Risk Level</th>
                    <th>Weather Risk</th>
                    <th>Currency Risk</th>
                    <th>Economy Risk</th>
                    <th>Political Risk</th>
                    <th>Overall Recommendation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row['country'] ?? '-' }}</td>
                        <td>{{ $row['risk_score'] ?? '-' }}</td>
                        <td>
                            @php
                                $level = strtolower($row['risk_level'] ?? '');
                                $badgeClass = 'bg-secondary';
                                if(str_contains($level, 'high')) $badgeClass = 'bg-danger';
                                elseif(str_contains($level, 'medium')) $badgeClass = 'bg-warning';
                                elseif(str_contains($level, 'low')) $badgeClass = 'bg-success';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['risk_level'] ?? '-' }}</span>
                        </td>
                        <td>{{ $row['weather_risk'] ?? '-' }}</td>
                        <td>{{ $row['currency_risk'] ?? '-' }}</td>
                        <td>{{ $row['economy_risk'] ?? '-' }}</td>
                        <td>{{ $row['political_risk'] ?? '-' }}</td>
                        <td>{{ $row['overall_recommendation'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
@endsection
