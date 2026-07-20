<?php

namespace App\Http\Controllers;

use App\Models\WatchList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchListController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = WatchList::with([
            'country.latestWeather',
            'country.latestCurrency',
            'country.latestRisk',
            'port.country',
            'route.originPort',
            'route.destinationPort',
        ])->where('user_id', $userId);

        if ($request->has('watch_type') && $request->watch_type !== 'all') {
            $query->where('watch_type', $request->watch_type);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $watchLists = $query->latest()->get();

        $totalItems = $watchLists->count();
        $highRiskItems = 0;
        $weatherAlerts = 0;
        $opportunities = 0; // we define opportunity if risk is Low and currency is Stable/Favorable

        foreach ($watchLists as $item) {
            $risk = 'Low';
            $weatherCondition = 'Clear';
            
            if ($item->watch_type === 'country') {
                $riskScore = $item->country->latestRisk->score ?? 0;
                if ($riskScore > 75) $risk = 'High';
                
                $weatherCondition = $item->country->latestWeather->condition ?? 'Clear';
                
            } elseif ($item->watch_type === 'port') {
                if ($item->port->risk_level === 'High' || $item->port->risk_level === 'Critical') {
                    $risk = 'High';
                }
            } elseif ($item->watch_type === 'route') {
                if ($item->route->risk_level === 'High') {
                    $risk = 'High';
                }
            }

            if ($risk === 'High') $highRiskItems++;
            if (str_contains(strtolower($weatherCondition), 'storm') || str_contains(strtolower($weatherCondition), 'extreme')) {
                $weatherAlerts++;
            }
            if ($risk === 'Low') {
                $opportunities++;
            }
        }

        return view('watch-list.index', compact(
            'watchLists', 
            'totalItems', 
            'highRiskItems', 
            'weatherAlerts', 
            'opportunities'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'watch_type' => 'required|in:country,port,route',
            'country_id' => 'nullable|exists:countries,id',
            'port_id'    => 'nullable|exists:ports,id',
            'route_id'   => 'nullable|exists:shipping_routes,id',
        ]);

        $userId = Auth::id();

        $exists = WatchList::where('user_id', $userId)
            ->where('watch_type', $request->watch_type)
            ->where(function($q) use ($request) {
                if ($request->country_id) $q->where('country_id', $request->country_id);
                if ($request->port_id) $q->where('port_id', $request->port_id);
                if ($request->route_id) $q->where('route_id', $request->route_id);
            })->exists();

        if (!$exists) {
            WatchList::create([
                'user_id'    => $userId,
                'watch_type' => $request->watch_type,
                'country_id' => $request->country_id,
                'port_id'    => $request->port_id,
                'route_id'   => $request->route_id,
                'status'     => 'monitoring',
            ]);
        }

        return back()->with('success', ucfirst($request->watch_type) . ' has been added to your Watch List.');
    }

    public function show($id)
    {
        $watchList = WatchList::with([
            'country.latestWeather',
            'country.latestCurrency',
            'country.latestRisk',
            'port.country',
            'route.originPort',
            'route.destinationPort',
        ])->where('user_id', Auth::id())->findOrFail($id);

        return view('watch-list.show', compact('watchList'));
    }

    public function destroy($id)
    {
        $watchList = WatchList::where('user_id', Auth::id())->findOrFail($id);
        $watchList->delete();

        return redirect()->route('watch-list.index')->with('success', 'Item removed from Watch List.');
    }
}
