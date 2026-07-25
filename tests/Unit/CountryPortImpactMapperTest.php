<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\News\CountryPortImpactMapper;
use App\Models\NewsCache;
use App\Models\Country;
use App\Models\Port;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CountryPortImpactMapperTest extends TestCase
{
    use RefreshDatabase;

    protected CountryPortImpactMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new CountryPortImpactMapper();

        // Create isolated test fixtures required by the mapper
        Country::create(['country_name' => 'China', 'country_code' => 'CN']);
        Country::create(['country_name' => 'United States of America', 'country_code' => 'US']);
        Country::create(['country_name' => 'Singapore', 'country_code' => 'SG']);

        Port::create([
            'name' => 'Keppel - (east Singapore)', 
            'country_iso2' => 'SG', 
            'country_name' => 'Singapore', 
            'code' => 'SGKEP',
            'region' => 'Asia',
            'city' => 'Singapore',
            'port_type' => 'Seaport',
            'latitude' => 1.29,
            'longitude' => 103.85
        ]);
    }

    public function test_country_exact_match()
    {
        $mapped = $this->mapper->map([
            'title' => 'Exports to China increase.',
            'category' => 'Trade',
            'trade_impact' => [
                'affected_countries' => ['China'],
                'impact_factors' => ['export']
            ]
        ]);

        $this->assertNotEmpty($mapped['mapped_countries']);
        $this->assertEquals('China', $mapped['mapped_countries'][0]['name']);
        $this->assertEquals('Direct', $mapped['mapped_countries'][0]['exposure']);
    }

    public function test_country_alias_match()
    {
        $mapped = $this->mapper->map([
            'title' => 'US tariffs affected.',
            'category' => 'Trade',
            'trade_impact' => [
                'affected_countries' => ['USA'],
                'impact_factors' => ['tariff']
            ]
        ]);

        $this->assertNotEmpty($mapped['mapped_countries']);
        $this->assertEquals('United States of America', $mapped['mapped_countries'][0]['name']);
    }

    public function test_regional_entity()
    {
        $mapped = $this->mapper->map([
            'title' => 'EU trade regulations updated.',
            'category' => 'Trade',
            'trade_impact' => [
                'affected_countries' => ['European Union'],
                'impact_factors' => ['policy']
            ]
        ]);

        $this->assertEmpty($mapped['mapped_countries']);
        $this->assertNotEmpty($mapped['regional_entities']);
        $this->assertEquals('European Union', $mapped['regional_entities'][0]['name']);
    }

    public function test_ambiguous_port_rejection()
    {
        $mapped = $this->mapper->map([
            'title' => 'Port of Singapore congestion increases.',
            'category' => 'Shipping',
            'trade_impact' => [
                'affected_countries' => ['Singapore'],
                'impact_factors' => ['congestion']
            ]
        ]);

        // Since "Singapore" is the candidate but it matches the country name, 
        // the hardened logic rejects it as a direct port match.
        $this->assertEmpty($mapped['mapped_ports']);
        $this->assertEquals('COUNTRY_LEVEL', $mapped['port_impact_type']);
    }

    public function test_exact_port_match()
    {
        $mapped = $this->mapper->map([
            'title' => 'Keppel Port terminal operations resumed.',
            'category' => 'Shipping',
            'trade_impact' => [
                'affected_countries' => ['Singapore'],
                'impact_factors' => ['congestion']
            ]
        ]);

        $this->assertNotEmpty($mapped['mapped_ports']);
        $this->assertEquals('Keppel - (east Singapore)', $mapped['mapped_ports'][0]['name']);
        $this->assertEquals('DIRECT', $mapped['port_impact_type']);
    }

    public function test_usb_port_rejection()
    {
        $mapped = $this->mapper->map([
            'title' => 'USB port shortage affects laptop manufacturer.',
            'category' => 'Manufacturing',
            'trade_impact' => [
                'affected_countries' => [],
                'impact_factors' => []
            ]
        ]);

        $this->assertEmpty($mapped['mapped_ports']);
        $this->assertEquals('NONE', $mapped['port_impact_type']);
    }

    public function test_airport_terminal_rejection()
    {
        $mapped = $this->mapper->map([
            'title' => 'Airport terminal operations disrupted.',
            'category' => 'Logistics',
            'trade_impact' => [
                'affected_countries' => [],
                'impact_factors' => []
            ]
        ]);

        $this->assertEmpty($mapped['mapped_ports']);
        $this->assertEquals('NONE', $mapped['port_impact_type']); // Because no country
    }

    public function test_country_level_maritime_exposure()
    {
        $mapped = $this->mapper->map([
            'title' => 'Container shipping congestion affects Singapore.',
            'category' => 'Shipping',
            'trade_impact' => [
                'affected_countries' => ['Singapore'],
                'impact_factors' => []
            ]
        ]);

        $this->assertEmpty($mapped['mapped_ports']);
        $this->assertEquals('COUNTRY_LEVEL', $mapped['port_impact_type']);
    }
}
