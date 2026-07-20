@extends('reports.exports.layout')

@section('title', 'Ports Report')

@section('export_buttons')
    <a href="{{ route('reports.ports.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('reports.ports.excel') }}" class="btn btn-success">
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
                    <th>Port Name</th>
                    <th>Country</th>
                    <th>Capacity</th>
                    <th>Active Ships</th>
                    <th>Congestion</th>
                    <th>Operational Status</th>
                    <th>Risk</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row['port_name'] ?? '-' }}</td>
                        <td>{{ $row['country'] ?? '-' }}</td>
                        <td>{{ $row['capacity'] ?? '-' }}</td>
                        <td>{{ $row['active_ships'] ?? '-' }}</td>
                        <td>{{ $row['congestion'] ?? '-' }}</td>
                        <td>
                            @php
                                $status = strtolower($row['operational_status'] ?? '');
                                $badgeClass = 'bg-secondary';
                                if(str_contains($status, 'operational') || str_contains($status, 'active')) $badgeClass = 'bg-success';
                                elseif(str_contains($status, 'closed') || str_contains($status, 'disrupted')) $badgeClass = 'bg-danger';
                                elseif(str_contains($status, 'restricted') || str_contains($status, 'limited')) $badgeClass = 'bg-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['operational_status'] ?? '-' }}</span>
                        </td>
                        <td>
                            @php
                                $risk = strtolower($row['risk'] ?? '');
                                $badgeClassRisk = 'bg-secondary';
                                if(str_contains($risk, 'high')) $badgeClassRisk = 'bg-danger';
                                elseif(str_contains($risk, 'medium')) $badgeClassRisk = 'bg-warning';
                                elseif(str_contains($risk, 'low')) $badgeClassRisk = 'bg-success';
                            @endphp
                            <span class="badge {{ $badgeClassRisk }}">{{ $row['risk'] ?? '-' }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
@endsection
