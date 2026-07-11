<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegativeWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('negative_words')->insert([
            ['word' => 'delay', 'weight' => 1],
            ['word' => 'crisis', 'weight' => 2],
            ['word' => 'war', 'weight' => 3],
            ['word' => 'inflation', 'weight' => 2],
            ['word' => 'conflict', 'weight' => 2],
        ]);
    }
}
