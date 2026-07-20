<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegativeWordSeeder extends Seeder
{
    public function run(): void
    {

        $words = [

            [
                'word'=>'delay',
                'weight'=>1
            ],

            [
                'word'=>'crisis',
                'weight'=>2
            ],

            [
                'word'=>'war',
                'weight'=>3
            ],

            [
                'word'=>'inflation',
                'weight'=>2
            ],

            [
                'word'=>'conflict',
                'weight'=>2
            ],

        ];


        foreach($words as $word)
        {

            DB::table('negative_words')
                ->updateOrInsert(
                    [
                        'word'=>$word['word']
                    ],
                    $word
                );

        }

    }
}