<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CountryComparisonController;
use App\Http\Controllers\RiskAnalysisController;
use App\Http\Controllers\WatchListController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\EconomyController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\TradePlannerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ShipController;

Route::get('/', function() {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::get('/countries', [CountryController::class,'index'])->name('countries.index');
    Route::get('/countries/search', [CountryController::class,'search'])->name('countries.search');
    Route::get('/countries/{country}', [CountryController::class,'show'])->name('countries.show');

    Route::get('/compare', [CountryComparisonController::class, 'index'])->name('compare.index');
    Route::post('/compare/save', [CountryComparisonController::class, 'save'])->name('compare.save');
    Route::get('/compare/export/pdf', [ReportExportController::class, 'compareLivePdf'])->name('compare.export.live.pdf');
    Route::get('/compare/export/excel', [ReportExportController::class, 'compareLiveExcel'])->name('compare.export.live.excel');

    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
    Route::get('/weather/{weather}', [WeatherController::class, 'show'])->name('weather.show');
    
    Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');
    Route::get('/currency/{currency}', [CurrencyController::class, 'show'])->name('currency.show');
    
    Route::get('/economy', [EconomyController::class, 'index'])->name('economy.index');
    Route::get('/economy/{economy}', [EconomyController::class, 'show'])->name('economy.show');
    
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/sync', [NewsController::class, 'sync'])->name('news.sync');
    Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
    Route::post('/news/sync/{country}', [NewsController::class, 'syncCountry'])->name('news.sync.country');

    Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
    Route::get('/ports/{port}', [PortController::class, 'show'])->name('ports.show');
    
    Route::get('/trade-planner', [TradePlannerController::class, 'index'])->name('trade-planner.index');
    
    Route::get('/risk-analysis', [RiskAnalysisController::class, 'index'])->name('risk-analysis.index');

    Route::resource('watch-list', WatchListController::class)->except(['create', 'edit', 'update']);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Export Routes
    Route::get('reports/compare/history', [ReportExportController::class, 'compareHistory'])->name('compare.history');
    Route::get('reports/compare/export/pdf-all', [ReportExportController::class, 'comparisonPdfAll'])->name('compare.pdf.all');
    Route::get('reports/compare/export/excel-all', [ReportExportController::class, 'comparisonExcelAll'])->name('compare.excel.all');
    Route::get('reports/compare/view/{id}', [ReportExportController::class, 'comparisonDetail'])->name('compare.view');
    Route::get('reports/compare/export/pdf/{id}', [ReportExportController::class, 'comparisonPdf'])->name('compare.pdf');
    Route::get('reports/compare/export/excel/{id}', [ReportExportController::class, 'comparisonExcel'])->name('compare.excel');
    Route::get('reports/trade-planner/view', [ReportExportController::class, 'tradePlannerView'])->name('trade-planner.view');
    Route::get('reports/trade-planner/export/pdf', [ReportExportController::class, 'tradePlannerPdf'])->name('trade-planner.pdf');
    Route::get('reports/trade-planner/export/excel', [ReportExportController::class, 'tradePlannerExcel'])->name('trade-planner.excel');
    Route::get('risk-analysis/view', [ReportExportController::class, 'riskAnalysisView'])->name('risk-analysis.view');
    Route::get('risk-analysis/pdf', [ReportExportController::class, 'riskAnalysisPdf'])->name('risk-analysis.pdf');
    Route::get('risk-analysis/excel', [ReportExportController::class, 'riskAnalysisExcel'])->name('risk-analysis.excel');

    Route::get('countries/view', [ReportExportController::class, 'countriesView'])->name('countries.view');
    Route::get('countries/pdf', [ReportExportController::class, 'countriesPdf'])->name('countries.pdf');
    Route::get('countries/excel', [ReportExportController::class, 'countriesExcel'])->name('countries.excel');

    Route::get('weather/view', [ReportExportController::class, 'weatherView'])->name('weather.view');
    Route::get('weather/pdf', [ReportExportController::class, 'weatherPdf'])->name('weather.pdf');
    Route::get('weather/excel', [ReportExportController::class, 'weatherExcel'])->name('weather.excel');

    Route::get('currency/view', [ReportExportController::class, 'currencyView'])->name('currency.view');
    Route::get('currency/pdf', [ReportExportController::class, 'currencyPdf'])->name('currency.pdf');
    Route::get('currency/excel', [ReportExportController::class, 'currencyExcel'])->name('currency.excel');

    Route::get('economy/view', [ReportExportController::class, 'economyView'])->name('economy.view');
    Route::get('economy/pdf', [ReportExportController::class, 'economyPdf'])->name('economy.pdf');
    Route::get('economy/excel', [ReportExportController::class, 'economyExcel'])->name('economy.excel');

    Route::get('news/view', [ReportExportController::class, 'newsView'])->name('news.view');
    Route::get('news/pdf', [ReportExportController::class, 'newsPdf'])->name('news.pdf');
    Route::get('news/excel', [ReportExportController::class, 'newsExcel'])->name('news.excel');

    Route::get('ports/view', [ReportExportController::class, 'portsView'])->name('ports.view');
    Route::get('ports/pdf', [ReportExportController::class, 'portsPdf'])->name('ports.pdf');
    Route::get('ports/excel', [ReportExportController::class, 'portsExcel'])->name('ports.excel');

    Route::get('watch-list/view', [ReportExportController::class, 'watchListView'])->name('watch-list.view');
    Route::get('watch-list/pdf', [ReportExportController::class, 'watchListPdf'])->name('watch-list.pdf');
    Route::get('watch-list/excel', [ReportExportController::class, 'watchListExcel'])->name('watch-list.excel');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
});

Route::get('/reports/trade-planner/pdf/{id}', [ReportExportController::class, 'singleSimulationPdf'])->name('reports.trade-planner.single-pdf');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    
    // Countries Management
    Route::get('/countries', [\App\Http\Controllers\Admin\CountryController::class, 'index'])->name('countries');
    Route::post('/countries/sync', [\App\Http\Controllers\Admin\CountryController::class, 'sync'])->name('countries.sync');
    Route::post('/countries', [\App\Http\Controllers\Admin\CountryController::class, 'store'])->name('countries.store');
    Route::put('/countries/{country}', [\App\Http\Controllers\Admin\CountryController::class, 'update'])->name('countries.update');
    Route::delete('/countries/{country}', [\App\Http\Controllers\Admin\CountryController::class, 'destroy'])->name('countries.destroy');
    // Ports Management
    Route::get('/ports', [\App\Http\Controllers\Admin\PortController::class, 'index'])->name('ports');
    Route::post('/ports/import', [\App\Http\Controllers\Admin\PortController::class, 'importCsv'])->name('ports.import');
    Route::post('/ports', [\App\Http\Controllers\Admin\PortController::class, 'store'])->name('ports.store');
    Route::put('/ports/{port}', [\App\Http\Controllers\Admin\PortController::class, 'update'])->name('ports.update');
    Route::patch('/ports/{port}/coordinates', [\App\Http\Controllers\Admin\PortController::class, 'updateCoordinates'])->name('ports.update_coordinates');
    Route::patch('/ports/{port}/status', [\App\Http\Controllers\Admin\PortController::class, 'updateStatus'])->name('ports.update_status');
    Route::delete('/ports/{port}', [\App\Http\Controllers\Admin\PortController::class, 'destroy'])->name('ports.destroy');
    // News Management
    Route::get('/news', [\App\Http\Controllers\Admin\NewsController::class, 'index'])->name('news');
    Route::patch('/news/{id}/category', [\App\Http\Controllers\Admin\NewsController::class, 'updateCategory'])->name('news.update_category');
    Route::patch('/news/{id}/status', [\App\Http\Controllers\Admin\NewsController::class, 'updateStatus'])->name('news.update_status');
    Route::delete('/news/{id}', [\App\Http\Controllers\Admin\NewsController::class, 'destroy'])->name('news.destroy');
    // Articles Management
    Route::get('/articles', [\App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('articles');
    Route::post('/articles', [\App\Http\Controllers\Admin\ArticleController::class, 'store'])->name('articles.store');
    Route::put('/articles/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('articles.destroy');
    // Sentiment Dictionary Management
    Route::get('/sentiment-dictionary', [\App\Http\Controllers\Admin\SentimentDictionaryController::class, 'index'])->name('sentiment-dictionary');
    Route::post('/sentiment-dictionary', [\App\Http\Controllers\Admin\SentimentDictionaryController::class, 'store'])->name('sentiment-dictionary.store');
    Route::put('/sentiment-dictionary/{id}', [\App\Http\Controllers\Admin\SentimentDictionaryController::class, 'update'])->name('sentiment-dictionary.update');
    Route::delete('/sentiment-dictionary/{id}', [\App\Http\Controllers\Admin\SentimentDictionaryController::class, 'destroy'])->name('sentiment-dictionary.destroy');
    Route::get('/api-monitor', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('api-monitor');
    Route::get('/system-logs', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('system-logs');
    // Admin Profile
    Route::get('/profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [\App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Admin\AdminProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/update', [\App\Http\Controllers\Admin\AdminSettingController::class, 'update'])->name('settings.update');
    });
