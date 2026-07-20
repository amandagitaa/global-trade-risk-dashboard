<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $query = WeatherData::with('country');

        if ($request->filled('country') && $request->country !== 'All Countries') {
            $query->where('country_id', $request->country);
        }

        if ($request->filled('condition') && $request->condition !== 'All') {
            $query->where('weather_status', strtolower($request->condition));
        }

        if ($request->filled('storm_risk') && $request->storm_risk !== 'All') {
            $risk = $request->storm_risk;
            if ($risk === 'Low') {
                $query->where('storm_risk', '<=', 25);
            } elseif ($risk === 'Medium') {
                $query->whereBetween('storm_risk', [26, 50]);
            } elseif ($risk === 'High') {
                $query->whereBetween('storm_risk', [51, 75]);
            } elseif ($risk === 'Extreme') {
                $query->where('storm_risk', '>', 75);
            }
        }

        $weatherData = $query->latest('recorded_at')->get();

        $totalCountries = $weatherData->pluck('country_id')->unique()->count();
        $averageTemperature = round($weatherData->avg('temperature') ?? 0, 1);
        $clearCount = $weatherData->where('weather_status', 'clear')->count();
        $rainCount = $weatherData->where('weather_status', 'rain')->count();
        $stormCount = $weatherData->where('weather_status', 'storm')->count();
        $extremeCount = $weatherData->where('weather_status', 'extreme')->count();
        $averageStormRisk = round($weatherData->avg('storm_risk') ?? 0, 1);
        $highestStormRisk = $weatherData->max('storm_risk') ?? 0;

        if ($averageStormRisk <= 25) {
            $tradeStatus = 'STABLE';
            $tradeColor = 'success';
        } elseif ($averageStormRisk <= 50) {
            $tradeStatus = 'WATCH';
            $tradeColor = 'warning';
        } elseif ($averageStormRisk <= 75) {
            $tradeStatus = 'ALERT';
            $tradeColor = 'orange';
        } else {
            $tradeStatus = 'CRITICAL';
            $tradeColor = 'danger';
        }

        $countries = Country::orderBy('country_name')->get();

        return view('weather.index', compact(
            'totalCountries',
            'averageTemperature',
            'clearCount',
            'rainCount',
            'stormCount',
            'extremeCount',
            'averageStormRisk',
            'highestStormRisk',
            'tradeStatus',
            'tradeColor',
            'weatherData',
            'countries'
        ));
    }

    public function show($id)
    {
        $weather = WeatherData::with('country')
                    ->findOrFail($id);

        return view('weather.show', compact('weather'));
    }
}