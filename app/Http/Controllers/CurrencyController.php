<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Currency Monitoring
     */
    public function index(Request $request)
    {
        $query = CurrencyRate::with('country');

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        if ($request->filled('currency')) {
            $query->where('target_currency', $request->currency);
        }

        /*
        |--------------------------------------------------------------------------
        | DATA TABLE
        |--------------------------------------------------------------------------
        */

        $currencies = $query
            ->latest('recorded_at')
            ->paginate(20);

        /*
        |--------------------------------------------------------------------------
        | FILTER DROPDOWN
        |--------------------------------------------------------------------------
        */

        $countries = Country::orderBy('country_name')->get();

        $currencyList = CurrencyRate::select('target_currency')
            ->distinct()
            ->orderBy('target_currency')
            ->pluck('target_currency');

        /*
        |--------------------------------------------------------------------------
        | STATISTIC
        |--------------------------------------------------------------------------
        */

        $totalCurrencies = CurrencyRate::count();

        $averageRate = round(
            CurrencyRate::avg('exchange_rate'),
            2
        );

        $highestRate = CurrencyRate::max('exchange_rate');

        $lowestRate = CurrencyRate::min('exchange_rate');

        return view('currency.index', compact(
            'currencies',
            'countries',
            'currencyList',
            'totalCurrencies',
            'averageRate',
            'highestRate',
            'lowestRate'
        ));
    }

    public function show(CurrencyRate $currency)
    {
        $currency->load('country');

        /*
        |--------------------------------------------------------------------------
        | Currency Analysis
        |--------------------------------------------------------------------------
        */

        $change = abs($currency->change_percentage);

        if ($change <= 1) {

            $status = 'STABLE';
            $statusColor = 'success';

            $impact = 'LOW';
            $impactColor = 'success';

            $recommendation = 'Proceed';

            $analysisTitle = 'Exchange Rate Stable';

            $analysisMessage =
                'Currency movement remains stable and international trade can continue normally.';

        } elseif ($change <= 3) {

            $status = 'WATCH';
            $statusColor = 'warning';

            $impact = 'MEDIUM';
            $impactColor = 'warning';

            $recommendation = 'Monitor';

            $analysisTitle = 'Minor Currency Fluctuation';

            $analysisMessage =
                'Exchange rate fluctuations should be monitored before making large transactions.';

        } elseif ($change <= 5) {

            $status = 'ALERT';
            $statusColor = 'orange';

            $impact = 'HIGH';
            $impactColor = 'danger';

            $recommendation = 'Review';

            $analysisTitle = 'High Currency Volatility';

            $analysisMessage =
                'Currency volatility may affect export pricing and import costs.';

        } else {

            $status = 'CRITICAL';
            $statusColor = 'danger';

            $impact = 'VERY HIGH';
            $impactColor = 'danger';

            $recommendation = 'Delay';

            $analysisTitle = 'Extreme Exchange Rate Movement';

            $analysisMessage =
                'Extreme currency fluctuations may significantly affect international trade.';

        }

        /*
        |--------------------------------------------------------------------------
        | Currency Stability Indicator
        |--------------------------------------------------------------------------
        */

        $stability = max(
            0,
            min(
                100,
                round(100 - ($change * 15))
            )
        );

        return view('currency.show', compact(
            'currency',

            'status',
            'statusColor',

            'impact',
            'impactColor',

            'recommendation',

            'analysisTitle',
            'analysisMessage',

            'stability'
        ));
    }
}