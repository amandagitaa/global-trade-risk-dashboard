@extends('reports.exports.layout')

@section('title', 'Country Comparison Report')

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
        <!-- Cover Page -->
        <div style="text-align: center; margin-top: 150px; page-break-after: always;">
            <h1 style="font-size: 36px; color: #0d6efd; margin-bottom: 20px;">Country Comparison Report</h1>
            <h3 style="color: #444; font-weight: normal; margin-bottom: 10px;">Trade Analyst: {{ auth()->user()->name ?? 'Administrator' }}</h3>
            <h4 style="color: #666; font-weight: normal; margin-bottom: 30px;">Generated Date: {{ date('d M Y H:i') }}</h4>
            <div style="display: inline-block; padding: 15px 30px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #ddd;">
                <h2 style="margin: 0; color: #333;">Total Comparisons: {{ count($data) }}</h2>
            </div>
        </div>

        @foreach($data as $index => $row)
            <div style="margin-bottom: 40px; page-break-inside: avoid;">
                <h4 style="margin-bottom: 10px; border-bottom: 2px solid #ccc; padding-bottom: 5px;">
                    Comparison #{{ count($data) - $index }}: {{ $row['country_a'] }} vs {{ $row['country_b'] }}
                    <span style="float: right; font-size: 12px; color: #666; font-weight: normal;">Date: {{ $row['comparison_date'] }}</span>
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
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ is_numeric($row['news_sentiment_a']) ? number_format((float)$row['news_sentiment_a'], 2) : $row['news_sentiment_a'] }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ is_numeric($row['news_sentiment_b']) ? number_format((float)$row['news_sentiment_b'], 2) : $row['news_sentiment_b'] }}</td>
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