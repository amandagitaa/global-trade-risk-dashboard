<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\exports\country-comparison.blade.php';

$bladeContent = <<<'EOD'
@extends('reports.exports.layout')

@section('title', 'Country Comparison Report History')

@section('export_buttons')
    @if(!isset($isPdf) || !$isPdf)
    <a href="{{ route('compare.pdf.all') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('compare.excel.all') }}" class="btn btn-success">
        Export Excel
    </a>
    @endif
@endsection

@section('content')
    
    @if(empty($data) || count($data) == 0)
        <div style="text-align: center; padding: 40px; color: #666; font-style: italic;">
            No saved country comparisons available
        </div>
    @else
        @foreach($data as $index => $row)
            <div style="margin-bottom: 30px; page-break-inside: avoid;">
                <h4 style="margin-bottom: 10px; border-bottom: 2px solid #ccc; padding-bottom: 5px;">
                    Comparison #{{ $index + 1 }}: {{ $row['country_a'] }} vs {{ $row['country_b'] }}
                    <span style="float: right; font-size: 12px; color: #666; font-weight: normal;">Date: {{ $row['comparison_date'] }} | By: {{ $row['created_by'] }}</span>
                </h4>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 8px; border: 1px solid #ddd; width: 33%;">Metric</th>
                            <th style="padding: 8px; border: 1px solid #ddd; width: 33%;">{{ $row['country_a'] }}</th>
                            <th style="padding: 8px; border: 1px solid #ddd; width: 33%;">{{ $row['country_b'] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">GDP</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['gdp_a'] }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['gdp_b'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Inflation</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['inflation_a'] }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['inflation_b'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Currency Status</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['currency_a'] }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['currency_b'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Weather Score</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['weather_a'] }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['weather_b'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Major Ports</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['ports_a'] }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['ports_b'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">News Sentiment</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ number_format((float)$row['news_sentiment_a'], 2) }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ number_format((float)$row['news_sentiment_b'], 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Overall Risk Score</td>
                            <td style="padding: 8px; border: 1px solid #ddd; color: #dc3545; font-weight: bold;">{{ $row['risk_score_a'] }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd; color: #dc3545; font-weight: bold;">{{ $row['risk_score_b'] }}</td>
                        </tr>
                    </tbody>
                </table>

                <div style="background-color: #fff3cd; border: 1px solid #ffe69c; padding: 10px; border-radius: 4px;">
                    <strong>Recommendation:</strong> {{ $row['recommendation'] }}<br>
                    <span style="font-size: 13px; color: #664d03;">{{ $row['summary'] }}</span>
                </div>
            </div>
        @endforeach
    @endif
    
@endsection
EOD;

file_put_contents($file, $bladeContent);
echo "PDF View completely overhauled to vertical sections.\n";
