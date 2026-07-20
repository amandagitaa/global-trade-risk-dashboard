<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\Trade\RiskAnalysisService;
use App\Services\RecommendationService;

class UpdateGlobalRiskCommand extends Command
{
    protected $signature = 'risk:update';

    protected $description = 'Update Global Risk Analysis for all countries';

    public function handle(
        RiskAnalysisService $riskAnalysis,
        RecommendationService $recommendationService
    )
    {
        $this->info('=========================================');
        $this->info('GLOBAL RISK ANALYSIS');
        $this->info('=========================================');

        $countries = Country::with([

            'latestWeather',

            'latestCurrency',

            'latestNews',

            'economicData',

            'ports'

        ])->get();

        $bar = $this->output->createProgressBar($countries->count());

        $bar->start();

        foreach ($countries as $country) {

            $riskAnalysis->calculate($country);

            $recommendationService->generate($country);

            $bar->advance();

        }

        $bar->finish();

        $this->newLine();

        $this->info('✔ Risk Analysis Updated');

        return self::SUCCESS;
    }
}