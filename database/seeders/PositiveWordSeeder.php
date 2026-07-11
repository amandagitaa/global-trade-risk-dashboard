<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositiveWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('positive_words')->insert([
            ['word' => 'growth', 'weight' => 2],
            ['word' => 'stable', 'weight' => 2],
            ['word' => 'profit', 'weight' => 2],
            ['word' => 'improve', 'weight' => 1],
            ['word' => 'efficient', 'weight' => 1],
        ]);
    }
}
