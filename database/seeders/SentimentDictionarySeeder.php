<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SentimentDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positives = [
            'growth', 'stable', 'increase', 'profit', 
            'improve', 'success', 'boost', 'recover'
        ];

        $negatives = [
            'war', 'crisis', 'delay', 'inflation', 
            'disaster', 'risk', 'loss', 'decline'
        ];

        foreach ($positives as $word) {
            \Illuminate\Support\Facades\DB::table('sentiment_dictionaries')->updateOrInsert(
                ['word' => $word, 'type' => 'positive'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach ($negatives as $word) {
            \Illuminate\Support\Facades\DB::table('sentiment_dictionaries')->updateOrInsert(
                ['word' => $word, 'type' => 'negative'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
