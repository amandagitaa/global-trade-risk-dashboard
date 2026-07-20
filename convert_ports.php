<?php
$portsFile = __DIR__ . '/database/seeders/data/ports.php';

if (!file_exists($portsFile)) {
    die("File tidak ditemukan:\n$portsFile\n");
}

$ports = require $portsFile;

/*
|--------------------------------------------------------------------------
| Country Code
|--------------------------------------------------------------------------
*/

$countryCodes = [

    'Indonesia' => 'ID',
    'Singapore' => 'SG',
    'Malaysia' => 'MY',
    'Thailand' => 'TH',
    'Vietnam' => 'VN',
    'Philippines' => 'PH',
    'China' => 'CN',
    'Japan' => 'JP',
    'South Korea' => 'KR',
    'India' => 'IN',
    'Pakistan' => 'PK',
    'Bangladesh' => 'BD',
    'Sri Lanka' => 'LK',
    'Taiwan' => 'TW',
    'Hong Kong' => 'HK',

    'United Arab Emirates' => 'AE',
    'Saudi Arabia' => 'SA',
    'Oman' => 'OM',
    'Qatar' => 'QA',
    'Kuwait' => 'KW',
    'Bahrain' => 'BH',
    'Iraq' => 'IQ',

    'Netherlands' => 'NL',
    'Belgium' => 'BE',
    'Germany' => 'DE',
    'France' => 'FR',
    'Italy' => 'IT',
    'Spain' => 'ES',
    'United Kingdom' => 'GB',
    'Greece' => 'GR',

    'United States' => 'US',
    'Canada' => 'CA',
    'Mexico' => 'MX',

    'Brazil' => 'BR',
    'Argentina' => 'AR',
    'Chile' => 'CL',
    'Peru' => 'PE',
    'Colombia' => 'CO',

    'Australia' => 'AU',
    'New Zealand' => 'NZ',

    'South Africa' => 'ZA',
    'Egypt' => 'EG',
    'Morocco' => 'MA',
    'Kenya' => 'KE',
    'Djibouti' => 'DJ',
    'Nigeria' => 'NG',
    'Tanzania' => 'TZ',
    'Ghana' => 'GH',

];

/*
|--------------------------------------------------------------------------
| Region
|--------------------------------------------------------------------------
*/

$regions = [

    'ID'=>'Southeast Asia',
    'SG'=>'Southeast Asia',
    'MY'=>'Southeast Asia',
    'TH'=>'Southeast Asia',
    'VN'=>'Southeast Asia',
    'PH'=>'Southeast Asia',

    'CN'=>'East Asia',
    'JP'=>'East Asia',
    'KR'=>'East Asia',
    'TW'=>'East Asia',
    'HK'=>'East Asia',

    'IN'=>'South Asia',
    'PK'=>'South Asia',
    'BD'=>'South Asia',
    'LK'=>'South Asia',

    'AE'=>'Middle East',
    'SA'=>'Middle East',
    'OM'=>'Middle East',
    'QA'=>'Middle East',
    'KW'=>'Middle East',
    'BH'=>'Middle East',
    'IQ'=>'Middle East',

    'NL'=>'Europe',
    'BE'=>'Europe',
    'DE'=>'Europe',
    'FR'=>'Europe',
    'IT'=>'Europe',
    'ES'=>'Europe',
    'GB'=>'Europe',
    'GR'=>'Europe',

    'US'=>'North America',
    'CA'=>'North America',
    'MX'=>'North America',

    'BR'=>'South America',
    'AR'=>'South America',
    'CL'=>'South America',
    'PE'=>'South America',
    'CO'=>'South America',

    'AU'=>'Oceania',
    'NZ'=>'Oceania',

    'ZA'=>'Africa',
    'EG'=>'Africa',
    'MA'=>'Africa',
    'KE'=>'Africa',
    'DJ'=>'Africa',
    'NG'=>'Africa',
    'TZ'=>'Africa',
    'GH'=>'Africa',

];

$new = [];

foreach ($ports as $port) {

    $countryCode = $countryCodes[$port['country']] ?? 'XX';

    /*
    |--------------------------------------------------------------------------
    | Priority
    |--------------------------------------------------------------------------
    */

    $priority = 'local';

    if (
        str_contains($port['name'], 'Singapore') ||
        str_contains($port['name'], 'Shanghai') ||
        str_contains($port['name'], 'Rotterdam') ||
        str_contains($port['name'], 'Los Angeles') ||
        str_contains($port['name'], 'Busan') ||
        str_contains($port['name'], 'Jebel Ali')
    ) {

        $priority = 'global';

    } elseif (

        $port['type'] == 'Container Port'

    ) {

        $priority = 'regional';

    }

    /*
    |--------------------------------------------------------------------------
    | Generate Code
    |--------------------------------------------------------------------------
    */

    $letters = strtoupper(
        substr(
            preg_replace('/[^A-Za-z]/', '', $port['name']),
            0,
            3
        )
    );

    $code = $countryCode . $letters;

    /*
    |--------------------------------------------------------------------------
    | Dummy Coordinate
    |--------------------------------------------------------------------------
    */

    $lat = round(mt_rand(-900000,900000)/10000,4);

    $lng = round(mt_rand(-1800000,1800000)/10000,4);

    $new[] = [

        'name'=>$port['name'],

        'country'=>$port['country'],

        'country_code'=>$countryCode,

        'city'=>$port['city'],

        'region'=>$regions[$countryCode] ?? 'Unknown',

        'latitude'=>$lat,

        'longitude'=>$lng,

        'port_type'=>$port['type'],

        'priority'=>$priority,

        'code'=>$code,

    ];

}

$content = "<?php\n\nreturn " . var_export($new,true) . ";";

file_put_contents($portsFile, $content);

echo "=====================================\n";
echo "Port dataset berhasil dikonversi.\n";
echo "File berhasil diperbarui:\n";
echo $portsFile . "\n";
echo "=====================================\n";

echo "Done! ports_new.php created.";