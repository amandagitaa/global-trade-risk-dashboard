<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;

class UpdateNewsOriginalUrl extends Command
{
    protected $signature = 'news:update-url';

    protected $description = 'Generate original source URL for existing news';


    public function handle()
    {

        $news = NewsCache::all();

        $count = 0;


        foreach ($news as $item) {


            $item->original_url = $this->generateUrl(
                $item->category
            );


            $item->save();


            $count++;

            $this->info(
                "Updated {$count}/{$news->count()}"
            );
        }


        $this->info(
            "Finished updating {$count} news"
        );


        return Command::SUCCESS;
    }



    private function generateUrl($category)
    {

        $sources = [

            'trade' =>
            'https://www.reuters.com/world/',


            'economy' =>
            'https://www.cnbc.com/economy/',


            'logistics' =>
            'https://www.bbc.com/news/business',


            'shipping' =>
            'https://www.maritime-executive.com/',


            'energy' =>
            'https://www.bloomberg.com/energy',


            'technology' =>
            'https://techcrunch.com/',


            'manufacturing' =>
            'https://www.industryweek.com/',


            'geopolitics' =>
            'https://www.aljazeera.com/'
        ];


        return $sources[$category]
            ?? 'https://www.reuters.com/world/';
    }
}