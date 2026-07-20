@extends('reports.exports.layout')

@section('title', 'Currency Report')

@section('export_buttons')
    <a href="{{ route('reports.currency.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('reports.currency.excel') }}" class="btn btn-success">
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
                    <th>Currency</th>
                    <th>Exchange Rate</th>
                    <th>Change %</th>
                    <th>Status</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row['country'] ?? '-' }}</td>
                        <td>{{ $row['currency'] ?? '-' }}</td>
                        <td>{{ $row['exchange_rate'] ?? '-' }}</td>
                        <td>
                            @php
                                $change = floatval($row['change_pct'] ?? 0);
                                $color = $change < 0 ? 'color: red;' : ($change > 0 ? 'color: green;' : '');
                            @endphp
                            <span style="{{ $color }}">{{ $row['change_pct'] ?? '-' }}</span>
                        </td>
                        <td>
                            @php
                                $status = strtolower($row['status'] ?? '');
                                $badgeClass = 'bg-secondary';
                                if(str_contains($status, 'stable')) $badgeClass = 'bg-success';
                                elseif(str_contains($status, 'volatile')) $badgeClass = 'bg-danger';
                                elseif(str_contains($status, 'warning')) $badgeClass = 'bg-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['status'] ?? '-' }}</span>
                        </td>
                        <td>{{ $row['updated_at'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
@endsection
