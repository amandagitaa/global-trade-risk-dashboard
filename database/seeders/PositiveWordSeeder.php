<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PositiveWordSeeder extends Seeder
{

    public function run(): void
    {

        $words = [

            [
                'word' => 'growth',
                'weight' => 2
            ],

            [
                'word' => 'stable',
                'weight' => 2
            ],

            [
                'word' => 'profit',
                'weight' => 2
            ],

            [
                'word' => 'improve',
                'weight' => 1
            ],

            [
                'word' => 'efficient',
                'weight' => 1
            ],

            [
                'word' => 'increase',
                'weight' => 1
            ],

            [
                'word' => 'success',
                'weight' => 2
            ],

            [
                'word' => 'expansion',
                'weight' => 2
            ],

            [
                'word' => 'investment',
                'weight' => 1
            ],

            [
                'word' => 'innovation',
                'weight' => 2
            ],

        ];


        foreach($words as $word)
        {

            DB::table('positive_words')
                ->updateOrInsert(

                    [
                        'word' => $word['word']
                    ],

                    $word

                );

        }

    }

}