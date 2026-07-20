<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_users' => Schema::hasTable('users') ? DB::table('users')->count() : 0,
            'total_countries' => Schema::hasTable('countries') ? DB::table('countries')->count() : 0,
            'total_ports' => Schema::hasTable('ports') ? DB::table('ports')->count() : 0,
            'total_news' => Schema::hasTable('news_cache') ? DB::table('news_cache')->count() : 0,
            'total_articles' => Schema::hasTable('articles') ? DB::table('articles')->count() : 0,
            'total_risk_scores' => Schema::hasTable('risk_scores') ? DB::table('risk_scores')->count() : 0,
            'total_watchlists' => Schema::hasTable('watchlists') ? DB::table('watchlists')->count() : 0,
            'total_comparisons' => Schema::hasTable('country_comparisons') ? DB::table('country_comparisons')->count() : 0,
        ];

        // Fetch user activity by month (created_at)
        $userActivity = DB::table('users')
            ->select(DB::raw('COUNT(*) as count'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $chartLabels = $userActivity->pluck('month')->map(function($month) {
            return date('M Y', strtotime($month . '-01'));
        });
        $chartData = $userActivity->pluck('count');

        return view('admin.dashboard', compact('data', 'chartLabels', 'chartData'));
    }
}
