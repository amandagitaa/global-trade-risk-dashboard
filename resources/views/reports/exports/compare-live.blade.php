<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Country Comparison Report</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0d6efd; padding-bottom: 15px; }
        .title { font-size: 24px; color: #0d6efd; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .subtitle { font-size: 14px; color: #666; margin-top: 5px; }
        
        .section-title { font-size: 16px; font-weight: bold; background-color: #f8f9fa; border-left: 4px solid #0d6efd; padding: 8px 15px; margin-top: 25px; margin-bottom: 15px; color: #333; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 10px; border: 1px solid #dee2e6; text-align: left; vertical-align: top; }
        th { background-color: #f1f3f5; font-weight: bold; color: #495057; width: 33.33%; }
        
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; color: white; text-align: center; }
        .bg-success { background-color: #198754; }
        .bg-primary { background-color: #0d6efd; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
        .bg-secondary { background-color: #6c757d; }

        .recommendation-box { background-color: #fff3cd; border: 1px solid #ffe69c; padding: 20px; border-radius: 8px; text-align: center; margin-top: 30px; }
        .winner-title { font-size: 20px; font-weight: bold; color: #856404; margin-bottom: 10px; }
        .winner-reason { font-size: 14px; margin-bottom: 5px; }
        
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 30px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="footer">
        Global Trade Risk Intelligence Platform &bull; Page <span class="page-number"></span>
    </div>

    <!-- COVER SECTION -->
    <div class="header">
        <h1 class="title">Country Comparison Report</h1>
        <div class="subtitle">
            <strong>Generated Date:</strong> {{ date('d M Y H:i') }} &nbsp;|&nbsp; 
            <strong>Trade Analyst:</strong> {{ auth()->user()->name ?? 'Administrator' }}
        </div>
        <h2 style="font-size: 20px; color: #444; margin-top: 20px;">
            {{ $res['country_name_a'] }} &nbsp;&nbsp; VS &nbsp;&nbsp; {{ $res['country_name_b'] }}
        </h2>
    </div>

    <!-- 1. Country Information -->
    <div class="section-title">1. Country Information</div>
    <table>
        <tr>
            <th>Parameter</th>
            <th>{{ $res['country_name_a'] }}</th>
            <th>{{ $res['country_name_b'] }}</th>
        </tr>
        <tr>
            <td><strong>Country</strong></td>
            <td>{{ $res['country_name_a'] }}</td>
            <td>{{ $res['country_name_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Capital</strong></td>
            <td>{{ $res['capital_a'] }}</td>
            <td>{{ $res['capital_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Region</strong></td>
            <td>{{ $res['region_a'] }}</td>
            <td>{{ $res['region_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Population</strong></td>
            <td>{{ number_format((float)$res['population_a']) }}</td>
            <td>{{ number_format((float)$res['population_b']) }}</td>
        </tr>
        <tr>
            <td><strong>Currency</strong></td>
            <td>{{ $res['currency_name_a'] }} ({{ $res['currency_code_a'] }})</td>
            <td>{{ $res['currency_name_b'] }} ({{ $res['currency_code_b'] }})</td>
        </tr>
        <tr>
            <td><strong>Language</strong></td>
            <td>{{ $res['language_a'] }}</td>
            <td>{{ $res['language_b'] }}</td>
        </tr>
    </table>

    <!-- 2. Economy Comparison -->
    <div class="section-title">2. Economy Comparison</div>
    <table>
        <tr>
            <th>Parameter</th>
            <th>{{ $res['country_name_a'] }}</th>
            <th>{{ $res['country_name_b'] }}</th>
        </tr>
        <tr>
            <td><strong>GDP</strong></td>
            <td>{{ $res['formatted_gdp_a'] }}</td>
            <td>{{ $res['formatted_gdp_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Inflation</strong></td>
            <td>{{ number_format((float)$res['inflation_a'], 1) }}%</td>
            <td>{{ number_format((float)$res['inflation_b'], 1) }}%</td>
        </tr>
        <tr>
            <td><strong>Export</strong></td>
            <td>{{ $res['formatted_exports_a'] }}</td>
            <td>{{ $res['formatted_exports_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Import</strong></td>
            <td>{{ $res['formatted_imports_a'] }}</td>
            <td>{{ $res['formatted_imports_b'] }}</td>
        </tr>
    </table>

    <!-- 3. Weather Comparison -->
    <div class="section-title">3. Weather Comparison</div>
    <table>
        <tr>
            <th>Parameter</th>
            <th>{{ $res['country_name_a'] }}</th>
            <th>{{ $res['country_name_b'] }}</th>
        </tr>
        <tr>
            <td><strong>Temperature</strong></td>
            <td>{{ $res['temp_a'] }}°C</td>
            <td>{{ $res['temp_b'] }}°C</td>
        </tr>
        <tr>
            <td><strong>Wind Speed</strong></td>
            <td>{{ $res['wind_a'] }} km/h</td>
            <td>{{ $res['wind_b'] }} km/h</td>
        </tr>
        <tr>
            <td><strong>Rainfall / Precip</strong></td>
            <td>{{ $res['rain_a'] }}%</td>
            <td>{{ $res['rain_b'] }}%</td>
        </tr>
        <tr>
            <td><strong>Weather Status</strong></td>
            <td>{{ $res['weather_status_a'] }}</td>
            <td>{{ $res['weather_status_b'] }}</td>
        </tr>
    </table>

    <!-- 4. Currency Comparison -->
    <div class="section-title">4. Currency Comparison</div>
    <table>
        <tr>
            <th>Parameter</th>
            <th>{{ $res['country_name_a'] }}</th>
            <th>{{ $res['country_name_b'] }}</th>
        </tr>
        <tr>
            <td><strong>Currency</strong></td>
            <td>{{ $res['currency_code_a'] }}</td>
            <td>{{ $res['currency_code_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Exchange Rate (vs USD)</strong></td>
            <td>{{ $res['exchange_a'] }}</td>
            <td>{{ $res['exchange_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Currency Impact (Status)</strong></td>
            <td>{{ $res['currency_status_a'] }}</td>
            <td>{{ $res['currency_status_b'] }}</td>
        </tr>
    </table>

    <!-- 5. Port Comparison -->
    <div class="section-title">5. Port Comparison</div>
    <table>
        <tr>
            <th>Parameter</th>
            <th>{{ $res['country_name_a'] }}</th>
            <th>{{ $res['country_name_b'] }}</th>
        </tr>
        <tr>
            <td><strong>Main Port</strong></td>
            <td>{{ $res['main_port_a'] }}</td>
            <td>{{ $res['main_port_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Major Ports Count</strong></td>
            <td>{{ $res['ports_count_a'] }}</td>
            <td>{{ $res['ports_count_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Congestion / Avg Risk</strong></td>
            <td>{{ $res['risk_port_a'] }} (Score)</td>
            <td>{{ $res['risk_port_b'] }} (Score)</td>
        </tr>
        <tr>
            <td><strong>Shipping Status</strong></td>
            <td>Normal</td>
            <td>Normal</td>
        </tr>
    </table>

    <!-- 6. News Sentiment -->
    <div class="section-title">6. News Sentiment</div>
    <table>
        <tr>
            <th>Parameter</th>
            <th>{{ $res['country_name_a'] }}</th>
            <th>{{ $res['country_name_b'] }}</th>
        </tr>
        <tr>
            <td><strong>Average Sentiment Score</strong></td>
            <td>{{ is_numeric($res['news_sentiment_a']) ? number_format((float)$res['news_sentiment_a'], 2) : $res['news_sentiment_a'] }}</td>
            <td>{{ is_numeric($res['news_sentiment_b']) ? number_format((float)$res['news_sentiment_b'], 2) : $res['news_sentiment_b'] }}</td>
        </tr>
    </table>

    <!-- 7. Risk Analysis -->
    <div class="section-title">7. Risk Analysis</div>
    <table>
        <tr>
            <th>Parameter</th>
            <th>{{ $res['country_name_a'] }}</th>
            <th>{{ $res['country_name_b'] }}</th>
        </tr>
        <tr>
            <td><strong>Weather Risk</strong></td>
            <td>{{ $res['risk_weather_a'] }}</td>
            <td>{{ $res['risk_weather_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Inflation Risk</strong></td>
            <td>{{ $res['risk_inflation_a'] }}</td>
            <td>{{ $res['risk_inflation_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Currency Risk</strong></td>
            <td>{{ $res['risk_currency_a'] }}</td>
            <td>{{ $res['risk_currency_b'] }}</td>
        </tr>
        <tr>
            <td><strong>Political Risk</strong></td>
            <td>{{ $res['risk_political_a'] }}</td>
            <td>{{ $res['risk_political_b'] }}</td>
        </tr>
        <tr>
            <td><strong style="color:#dc3545">Overall Risk Score</strong></td>
            <td><strong style="color:#dc3545">{{ $res['risk_final_a'] }}</strong></td>
            <td><strong style="color:#dc3545">{{ $res['risk_final_b'] }}</strong></td>
        </tr>
    </table>

    <!-- 8. Trade Recommendation -->
    <div class="section-title">8. Trade Recommendation</div>
    <div class="recommendation-box">
        <div class="winner-title">Recommended: {{ $res['winner_name'] }}</div>
        
        @if(isset($res['reasons']) && count($res['reasons']) > 0)
            <div style="margin: 15px 0;">
                @foreach($res['reasons'] as $reason)
                    <div class="winner-reason">&bull; {{ $reason }}</div>
                @endforeach
            </div>
        @endif
        
        <div style="margin-top: 15px; font-weight: bold; color: #333;">
            {{ $res['conclusion'] }}
        </div>
    </div>

</body>
</html>
