@extends('reports.exports.layout')

@section('title', 'Economy Report')

@section('export_buttons')
    <a href="{{ route('reports.economy.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('reports.economy.excel') }}" class="btn btn-success">
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
                    <th>GDP</th>
                    <th>Inflation</th>
                    <th>Unemployment</th>
                    <th>Export</th>
                    <th>Import</th>
                    <th>Economic Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row['country'] ?? '-' }}</td>
                        <td>{{ $row['gdp'] ?? '-' }}</td>
                        <td>{{ $row['inflation'] ?? '-' }}</td>
                        <td>{{ $row['unemployment'] ?? '-' }}</td>
                        <td>{{ $row['export'] ?? '-' }}</td>
                        <td>{{ $row['import'] ?? '-' }}</td>
                        <td>
                            @php
                                $status = strtolower($row['economic_status'] ?? '');
                                $badgeClass = 'bg-secondary';
                                if(str_contains($status, 'stable') || str_contains($status, 'growing')) $badgeClass = 'bg-success';
                                elseif(str_contains($status, 'recession') || str_contains($status, 'crisis')) $badgeClass = 'bg-danger';
                                elseif(str_contains($status, 'stagnant') || str_contains($status, 'warning')) $badgeClass = 'bg-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['economic_status'] ?? '-' }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
@endsection
