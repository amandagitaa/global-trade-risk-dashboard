@extends('reports.exports.layout')

@section('title', 'Watch List Report')

@section('export_buttons')
    <a href="{{ route('reports.watch-list.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('reports.watch-list.excel') }}" class="btn btn-success">
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
                    <th>Watch Type</th>
                    <th>Country / Port / Route</th>
                    <th>Current Risk</th>
                    <th>Weather</th>
                    <th>Currency</th>
                    <th>Monitoring Status</th>
                    <th>Added Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row['watch_type'] ?? '-' }}</td>
                        <td>{{ $row['name'] ?? '-' }}</td>
                        <td>
                            @php
                                $risk = strtolower($row['current_risk'] ?? '');
                                $badgeClassRisk = 'bg-secondary';
                                if(str_contains($risk, 'high')) $badgeClassRisk = 'bg-danger';
                                elseif(str_contains($risk, 'medium')) $badgeClassRisk = 'bg-warning';
                                elseif(str_contains($risk, 'low')) $badgeClassRisk = 'bg-success';
                            @endphp
                            <span class="badge {{ $badgeClassRisk }}">{{ $row['current_risk'] ?? '-' }}</span>
                        </td>
                        <td>{{ $row['weather'] ?? '-' }}</td>
                        <td>{{ $row['currency'] ?? '-' }}</td>
                        <td>
                            @php
                                $status = strtolower($row['monitoring_status'] ?? '');
                                $badgeClass = 'bg-secondary';
                                if(str_contains($status, 'active')) $badgeClass = 'bg-success';
                                elseif(str_contains($status, 'paused')) $badgeClass = 'bg-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['monitoring_status'] ?? '-' }}</span>
                        </td>
                        <td>{{ $row['added_date'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
@endsection
