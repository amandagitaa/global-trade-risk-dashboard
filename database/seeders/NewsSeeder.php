<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Support\Str;
use Carbon\Carbon;


class NewsSeeder extends Seeder
{

    /*
    |--------------------------------------------------------------------------
    | News Sources
    |--------------------------------------------------------------------------
    */

    protected array $sources = [

        'Reuters',
        'Bloomberg',
        'CNBC',
        'BBC Business',
        'Financial Times',
        'The Economist',
        'Nikkei Asia',
        'Forbes',
        'AP News',
        'MarketWatch',
        'Trade Monitor',
        'Global Logistics Daily',

    ];


    /*
    |--------------------------------------------------------------------------
    | Authors
    |--------------------------------------------------------------------------
    */

    protected array $authors = [

        'Michael Johnson',
        'Emily Carter',
        'David Brown',
        'Sophia Wilson',
        'Daniel Lee',
        'James Anderson',
        'Olivia Smith',
        'William Taylor',

    ];


    /*
    |--------------------------------------------------------------------------
    | News Images
    |--------------------------------------------------------------------------
    */

    protected array $images = [

        'trade' =>
        '/images/news/trade.jpg',

        'shipping' =>
        '/images/news/shipping.jpg',

        'logistics' =>
        '/images/news/logistics.jpg',

        'economy' =>
        '/images/news/economy.jpg',

        'technology' =>
        '/images/news/technology.jpg',

        'energy' =>
        '/images/news/energy.jpg',

        'manufacturing' =>
        '/images/news/manufacturing.jpg',

        'geopolitics' =>
        '/images/news/geopolitics.jpg',

    ];



    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    protected array $categories = [

        'trade',
        'shipping',
        'logistics',
        'economy',
        'technology',
        'energy',
        'manufacturing',
        'geopolitics',

    ];



    /*
    |--------------------------------------------------------------------------
    | Country Economic Profile
    |--------------------------------------------------------------------------
    */

    protected array $countryProfiles = [

        'Indonesia' => [

            'Palm Oil',
            'Nickel',
            'Coal',
            'Coffee',
            'Rubber',

        ],


        'Malaysia' => [

            'Palm Oil',
            'Electronics',
            'Natural Gas',

        ],


        'Singapore' => [

            'Shipping',
            'Finance',
            'Logistics',

        ],


        'China' => [

            'Manufacturing',
            'Steel',
            'Electric Vehicle',

        ],


        'Japan' => [

            'Semiconductor',
            'Automotive',
            'Robotics',

        ],


        'South Korea' => [

            'Battery',
            'Shipbuilding',
            'Technology',

        ],


        'Germany' => [

            'Automotive',
            'Machinery',
            'Chemical',

        ],


        'United States' => [

            'Technology',
            'Artificial Intelligence',
            'Oil',

        ],


        'India' => [

            'IT Services',
            'Pharmaceutical',
            'Steel',

        ],


        'Brazil' => [

            'Soybean',
            'Coffee',
            'Iron Ore',

        ],


        'Saudi Arabia' => [

            'Oil',
            'Energy',
            'Petrochemical',

        ],

    ];



    /*
    |--------------------------------------------------------------------------
    | Country Intelligence
    |--------------------------------------------------------------------------
    */

    protected array $countryIntelligence = [


        'Indonesia' => [

            'currency'=>'IDR',

            'region'=>'ASEAN',

            'main_port'=>'Port of Tanjung Priok',

        ],


        'Singapore'=>[

            'currency'=>'SGD',

            'region'=>'ASEAN',

            'main_port'=>'Port of Singapore',

        ],


        'China'=>[

            'currency'=>'CNY',

            'region'=>'East Asia',

            'main_port'=>'Port of Shanghai',

        ],


        'Japan'=>[

            'currency'=>'JPY',

            'region'=>'East Asia',

            'main_port'=>'Port of Yokohama',

        ],


        'Germany'=>[

            'currency'=>'EUR',

            'region'=>'European Union',

            'main_port'=>'Port of Hamburg',

        ],


        'United States'=>[

            'currency'=>'USD',

            'region'=>'North America',

            'main_port'=>'Port of Los Angeles',

        ],


    ];



    /*
    |--------------------------------------------------------------------------
    | Regions
    |--------------------------------------------------------------------------
    */

    protected array $regions = [

        'ASEAN'=>[

            'Indonesia',
            'Malaysia',
            'Singapore',
            'Thailand',
            'Vietnam',

        ],


        'East Asia'=>[

            'China',
            'Japan',
            'South Korea',

        ],


        'European Union'=>[

            'Germany',
            'France',
            'Italy',

        ],


        'North America'=>[

            'United States',
            'Canada',
            'Mexico',

        ],


    ];



    /*
    |--------------------------------------------------------------------------
    | Dynamic Events
    |--------------------------------------------------------------------------
    */

    protected array $events = [

        [
            'name'=>'Export Growth',
            'category'=>'trade',
        ],


        [
            'name'=>'New Trade Agreement',
            'category'=>'trade',
        ],


        [
            'name'=>'Port Expansion',
            'category'=>'shipping',
        ],


        [
            'name'=>'Technology Investment',
            'category'=>'technology',
        ],


        [
            'name'=>'Manufacturing Expansion',
            'category'=>'manufacturing',
        ],


        [
            'name'=>'Energy Investment',
            'category'=>'energy',
        ],


        [
            'name'=>'Port Congestion',
            'category'=>'shipping',
        ],


        [
            'name'=>'Supply Chain Disruption',
            'category'=>'logistics',
        ],


        [
            'name'=>'Trade War',
            'category'=>'geopolitics',
        ],


        [
            'name'=>'New Tariff',
            'category'=>'trade',
        ],


    ];



    /*
    |--------------------------------------------------------------------------
    | Headline Templates
    |--------------------------------------------------------------------------
    */

    protected array $headlineTemplates = [

        'Export Growth'=>[

            '{country} expands {industry} exports amid rising global demand',

            '{country} records strong growth in {industry} exports',

            '{country} strengthens international trade through {industry}',

        ],


        'New Trade Agreement'=>[

            '{country} signs new trade agreement to support {industry}',

            '{country} opens new market opportunities',

        ],


        'Port Expansion'=>[

            '{country} expands port capacity for global trade',

            '{port} becomes strategic logistics hub',

        ],


        'Technology Investment'=>[

            '{country} increases investment in {industry}',

            '{country} accelerates digital transformation',

        ],


        'Trade War'=>[

            'Trade tensions affect {industry} exports in {country}',

            '{country} faces international trade pressure',

        ],

    ];

/*
    |--------------------------------------------------------------------------
    | RUN SEEDER
    |--------------------------------------------------------------------------
    */

    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | Clear Existing News
        |--------------------------------------------------------------------------
        */

        NewsCache::truncate();



        /*
        |--------------------------------------------------------------------------
        | Get Countries
        |--------------------------------------------------------------------------
        */

        $countries = Country::orderBy('country_name')
            ->limit(250)
            ->get();



        /*
        |--------------------------------------------------------------------------
        | Generate News
        |--------------------------------------------------------------------------
        */

        foreach($countries as $country){


            for($i = 1; $i <= 10; $i++){



                /*
                |--------------------------------------------------------------------------
                | Country Name
                |--------------------------------------------------------------------------
                */

                $countryName = $country->country_name;



                /*
                |--------------------------------------------------------------------------
                | Random Event
                |--------------------------------------------------------------------------
                */

                $event = $this->events[
                    array_rand($this->events)
                ];



                /*
                |--------------------------------------------------------------------------
                | Category
                |--------------------------------------------------------------------------
                */

                $category = $event['category'];



                /*
                |--------------------------------------------------------------------------
                | Generate Content Data
                |--------------------------------------------------------------------------
                */

                $title = $this->generateTitle(

                    $countryName,

                    $event['name']

                );



                $description = $this->generateDescription(

                    $countryName,

                    $event['name']

                );



                $content = $this->generateContent(

                    $countryName,

                    $event['name']

                );



                /*
                |--------------------------------------------------------------------------
                | Source & Author
                |--------------------------------------------------------------------------
                */

                $source = $this->sources[

                    array_rand($this->sources)

                ];


                $author = $this->authors[

                    array_rand($this->authors)

                ];



                /*
                |--------------------------------------------------------------------------
                | Sentiment
                |--------------------------------------------------------------------------
                */

                $sentiment =
                    $this->generateSentiment(
                        $event['name']
                    );



                /*
                |--------------------------------------------------------------------------
                | Risk
                |--------------------------------------------------------------------------
                */

                $risk =
                    $this->calculateRisk(
                        $category,
                        $sentiment
                    );



                /*
                |--------------------------------------------------------------------------
                | Quality
                |--------------------------------------------------------------------------
                */

                $quality =
                    $this->calculateQuality(
                        $source
                    );



                /*
                |--------------------------------------------------------------------------
                | Trade Impact
                |--------------------------------------------------------------------------
                */

                $impact =
                    $this->generateTradeImpact(

                        $category,

                        $sentiment['label']

                    );



                /*
                |--------------------------------------------------------------------------
                | Country Relationship
                |--------------------------------------------------------------------------
                */

                $relationship =
                    $this->generateRelationship(

                        $countryName,

                        $category

                    );



                /*
                |--------------------------------------------------------------------------
                | Insert News
                |--------------------------------------------------------------------------
                */


                NewsCache::create([


                    'country_id'=>$country->id,


                    'title'=>$title,


                    'description'=>$description,


                    'content'=>$content,


                    'category'=>$category,


                    'source'=>$source,


                    'author'=>$author,


                    'image_url'=>

                        $this->images[$category]
                        ??
                        '/images/news-placeholder.jpg',



                    'original_url'=>

                        '/news/' .

                        Str::slug($title)

                        .'-'

                        .Str::random(8),

                    'url' => $this->generateSourceUrl(
                        $country->country_name,
                        $category
                    ),

                    'published_at'=>

                        Carbon::now()

                        ->subDays(

                            rand(0,90)

                        ),



                    'sentiment'=>

                        $sentiment['label'],



                    'positive_score'=>

                        $sentiment['positive'],



                    'negative_score'=>

                        $sentiment['negative'],



                    'risk_score'=>

                        $risk['score'],



                    'risk_level'=>

                        $risk['level'],



                    'quality_score'=>

                        $quality['score'],



                    'reliability_level'=>

                        $quality['level'],



                    'verification_status'=>

                        $quality['status'],



                    'trade_impact'=>

                        json_encode($impact),



                    'trade_relationship'=>

                        json_encode($relationship),



                ]);

            }


            echo "Generated news for {$countryName}\n";


        }



        echo "\n2500 News Generated Successfully\n";

    }
/*
|--------------------------------------------------------------------------
| Country Information
|--------------------------------------------------------------------------
*/

protected function countryInfo(string $country): array
{

    return $this->countryIntelligence[$country]

        ??

        [

            'currency' => 'USD',

            'region' => 'Global',

            'main_port' => 'International Port',

        ];

}

/*
|--------------------------------------------------------------------------
| Country Industry
|--------------------------------------------------------------------------
*/

protected function getCountryIndustry(
    string $country
): string
{

    if(isset($this->countryProfiles[$country])){


        return $this->countryProfiles[$country][

            array_rand(
                $this->countryProfiles[$country]
            )

        ];


    }


    return collect([

        'Trade',

        'Manufacturing',

        'Technology',

        'Agriculture',

        'Logistics',

    ])->random();


}
/*
|--------------------------------------------------------------------------
| Generate Headline
|--------------------------------------------------------------------------
*/

protected function generateTitle(
    string $country,
    string $event
): string
{

    $industry = $this->getCountryIndustry($country);


    $info = $this->countryInfo($country);



    $templates = $this->headlineTemplates[$event]

        ??

        [

            '{country} reports latest trade development'

        ];



    $template = $templates[

        array_rand($templates)

    ];



    return str_replace(

        [

            '{country}',

            '{industry}',

            '{port}',

            '{region}',

            '{currency}',

        ],


        [

            $country,

            $industry,

            $info['main_port'],

            $info['region'],

            $info['currency'],

        ],


        $template

    );


}
/*
|--------------------------------------------------------------------------
| Generate Description
|--------------------------------------------------------------------------
*/

protected function generateDescription(
    string $country,
    string $event
): string
{

    $industry = $this->getCountryIndustry($country);

    $info = $this->countryInfo($country);


    $descriptions = [


        "{$country} continues strengthening its {$industry} sector following the latest {$event} developments. The initiative is expected to influence international trade performance.",


        "Recent {$event} activities in {$country} highlight changes within the {$industry} industry and create new opportunities across global markets.",


        "Market analysts are monitoring {$country}'s {$industry} sector as businesses adapt to changing trade conditions and supply chain challenges.",


        "The development of {$industry} in {$country} is supported by improvements in logistics infrastructure and international cooperation.",


        "Trade activities through {$info['main_port']} continue supporting economic growth in the {$info['region']} region.",


    ];


    return $descriptions[

        array_rand($descriptions)

    ];


}

/*
|--------------------------------------------------------------------------
| Generate Article Content
|--------------------------------------------------------------------------
*/

protected function generateContent(
    string $country,
    string $event
): string
{

    $industry = $this->getCountryIndustry($country);

    $info = $this->countryInfo($country);



    $paragraphs = [


        "{$country} continues to develop its {$industry} sector as part of broader economic and trade strategies. Recent {$event} developments have attracted attention from international businesses and investors.",



        "Industry observers believe that improvements in technology, infrastructure, and operational efficiency will strengthen the position of {$country} in global markets. Companies operating in the {$industry} sector continue adapting to changing international demand.",



        "Trade activities connected through {$info['main_port']} remain an important factor supporting supply chain performance. The country continues improving logistics capabilities to maintain competitiveness within the {$info['region']} region.",



        "Economic analysts suggest that future performance will depend on global market conditions, regulatory changes, and cooperation between international trading partners.",



        "The outlook for {$industry} remains closely monitored as businesses evaluate new opportunities and potential risks affecting global trade flows.",



    ];



    return implode(

        "\n\n",

        $paragraphs

    );


}
/*
|--------------------------------------------------------------------------
| Generate Sentiment Score
|--------------------------------------------------------------------------
*/

protected function generateSentiment(
    string $event
): array
{

    $negativeEvents = [

        'Trade War',

        'Port Congestion',

        'Supply Chain Disruption',

        'New Tariff',

    ];


    $neutralEvents = [

        'Energy Investment',

        'Technology Investment',

    ];



    /*
    |--------------------------------------------------------------------------
    | Negative News
    |--------------------------------------------------------------------------
    */

    if(in_array($event,$negativeEvents)){


        return [

            'label'=>'negative',

            'positive'=>rand(1,3),

            'negative'=>rand(7,10),

        ];


    }



    /*
    |--------------------------------------------------------------------------
    | Neutral News
    |--------------------------------------------------------------------------
    */

    if(in_array($event,$neutralEvents)){


        return [

            'label'=>'neutral',

            'positive'=>rand(3,5),

            'negative'=>rand(3,5),

        ];


    }



    /*
    |--------------------------------------------------------------------------
    | Positive News
    |--------------------------------------------------------------------------
    */


    return [

        'label'=>'positive',

        'positive'=>rand(7,10),

        'negative'=>rand(0,2),

    ];


}
/*
|--------------------------------------------------------------------------
| Calculate Risk
|--------------------------------------------------------------------------
*/

protected function calculateRisk(
    string $category,
    array $sentiment
): array
{


    $negativeScore =
        $sentiment['negative'];



    if($negativeScore <= 2){


        $level='Low';


    }
    elseif($negativeScore <=5){


        $level='Medium';


    }
    elseif($negativeScore <=8){


        $level='High';


    }
    else{


        $level='Critical';


    }



    return [

        'score'=>$negativeScore,

        'level'=>$level,

    ];


}
/*
|--------------------------------------------------------------------------
| Calculate News Reliability
|--------------------------------------------------------------------------
*/

protected function calculateQuality(
    string $source
): array
{


    $premiumSources=[

        'Reuters',

        'Bloomberg',

        'BBC Business',

        'Financial Times',

        'Nikkei Asia',

    ];



    if(in_array($source,$premiumSources)){


        return [

            'score'=>rand(90,98),

            'level'=>'High',

            'status'=>'Verified Analysis',

        ];


    }



    return [

        'score'=>rand(75,89),

        'level'=>'Medium',

        'status'=>'Reviewed Analysis',

    ];



}
/*
|--------------------------------------------------------------------------
| Generate Trade Impact
|--------------------------------------------------------------------------
*/

protected function generateTradeImpact(
    string $category,
    string $sentiment
): array
{


    $impact = [

        'shipping'=>'Medium',

        'logistics'=>'Medium',

        'export'=>'Medium',

        'import'=>'Medium',

        'currency'=>'Medium',

    ];



    /*
    |--------------------------------------------------------------------------
    | Category Impact
    |--------------------------------------------------------------------------
    */


    if($category == 'shipping'){


        $impact['shipping'] = 'High';

        $impact['logistics'] = 'High';


    }



    if($category == 'logistics'){


        $impact['logistics'] = 'High';


        $impact['shipping'] = 'Medium';


    }



    if($category == 'economy'){


        $impact['currency'] = 'High';


    }



    if($category == 'trade'){


        $impact['export'] = 'High';

        $impact['import'] = 'High';


    }



    if($category == 'energy'){


        $impact['currency'] = 'Medium';

        $impact['import'] = 'High';


    }



    /*
    |--------------------------------------------------------------------------
    | Sentiment Adjustment
    |--------------------------------------------------------------------------
    */


    if($sentiment == 'negative'){


        foreach($impact as $key=>$value){


            $impact[$key]='High';


        }


    }



    return $impact;


}
/*
|--------------------------------------------------------------------------
| Generate Country Relationship
|--------------------------------------------------------------------------
*/

protected function generateRelationship(
    string $country,
    string $category
): array
{


    $info = $this->countryInfo($country);



    return [

        'country'=>$country,


        'region'=>

            $info['region'],


        'currency'=>

            $info['currency'],


        'main_port'=>

            $info['main_port'],


        'category'=>$category,


        'industry'=>

            $this->getCountryIndustry($country),


    ];


}
private function generateSourceUrl($country, $category)
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
        'https://www.techcrunch.com/',

        'manufacturing' =>
        'https://www.industryweek.com/',

        'geopolitics' =>
        'https://www.aljazeera.com/'
    ];


    return $sources[$category]
        ?? 'https://www.reuters.com/world/';
}
}