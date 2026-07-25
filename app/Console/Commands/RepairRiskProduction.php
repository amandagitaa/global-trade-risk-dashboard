<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairRiskProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'risk:repair-production {--dry-run : Execute a dry run without updating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repair final_score and risk_level in risk_scores table based on existing component scores.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $totalRiskScores = 0;
        $rowsRequiringScoreFix = 0;
        $rowsRequiringLevelFix = 0;
        $totalRowsRequiringUpdate = 0;
        
        $examples = [];
        
        // Use DB facade to ensure we only touch risk_scores
        $riskScores = DB::table('risk_scores')->get();
        $totalRiskScores = $riskScores->count();
        
        $updatesToApply = [];

        foreach ($riskScores as $score) {
            $expectedFinal = round(
                ($score->weather_score * 0.25) +
                ($score->currency_score * 0.20) +
                ($score->economic_score * 0.15) +
                ($score->port_score * 0.20) +
                ($score->news_score * 0.20),
                2
            );
            
            $expectedLevel = $this->determineRiskLevel($expectedFinal);
            
            // Check if final_score or risk_level differ
            $scoreDiffers = abs($score->final_score - $expectedFinal) > 0.0001;
            $levelDiffers = $score->risk_level !== $expectedLevel;
            
            if ($scoreDiffers || $levelDiffers) {
                $totalRowsRequiringUpdate++;
                
                if ($scoreDiffers) {
                    $rowsRequiringScoreFix++;
                }
                
                if ($levelDiffers) {
                    $rowsRequiringLevelFix++;
                }
                
                $updatesToApply[] = [
                    'id' => $score->id,
                    'final_score' => $expectedFinal,
                    'risk_level' => $expectedLevel,
                ];
                
                if (count($examples) < 10) {
                    $examples[] = [
                        'country_id' => $score->country_id,
                        'old_score' => $score->final_score,
                        'new_score' => $expectedFinal,
                        'old_level' => $score->risk_level,
                        'new_level' => $expectedLevel
                    ];
                }
            }
        }
        
        if ($isDryRun) {
            $this->info('=== RISK SCORE REPAIR DRY RUN ===');
            $this->line('');
            $this->info("Total Risk Scores: {$totalRiskScores}");
            $this->info("Rows Requiring Score Fix: {$rowsRequiringScoreFix}");
            $this->info("Rows Requiring Level Fix: {$rowsRequiringLevelFix}");
            $this->info("Total Rows Requiring Update: {$totalRowsRequiringUpdate}");
            
            if (count($examples) > 0) {
                $this->line('');
                $this->info('Contoh maksimal 10:');
                foreach ($examples as $example) {
                    $this->line('');
                    $this->line("Country ID: {$example['country_id']}");
                    $this->line("Old Score -> New Score: {$example['old_score']} -> {$example['new_score']}");
                    $this->line("Old Level -> New Level: {$example['old_level']} -> {$example['new_level']}");
                }
            }
            return Command::SUCCESS;
        }

        try {
            DB::transaction(function () use ($updatesToApply) {
                foreach ($updatesToApply as $update) {
                    DB::table('risk_scores')
                        ->where('id', $update['id'])
                        ->update([
                            'final_score' => $update['final_score'],
                            'risk_level' => $update['risk_level']
                        ]);
                }
            });
            
            $this->info('=== PRODUCTION RISK SCORE REPAIR ===');
            $this->line('');
            $this->info("Total Risk Scores: {$totalRiskScores}");
            $this->info("Rows Updated: {$totalRowsRequiringUpdate}");
            $this->info("Score Fixes: {$rowsRequiringScoreFix}");
            $this->info("Risk Level Fixes: {$rowsRequiringLevelFix}");
            $this->info("Transaction Status: COMMITTED");
            
        } catch (\Exception $e) {
            $this->error('Transaction failed. ROLLBACK applied.');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
    
    /**
     * Determine risk level based on the expected score.
     *
     * @param float $score
     * @return string
     */
    private function determineRiskLevel($score)
    {
        if ($score <= 20) {
            return 'safe';
        } elseif ($score <= 40) {
            return 'stable';
        } elseif ($score <= 60) {
            return 'alert';
        } elseif ($score <= 80) {
            return 'dangerous';
        } else {
            return 'critical';
        }
    }
}
