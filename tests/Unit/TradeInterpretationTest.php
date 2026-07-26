<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\News\TradeImpactAnalyzer;
use App\Services\News\TradeIntelligenceSummaryService;
use App\Services\News\CountryPortImpactMapper;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Country;

class TradeInterpretationTest extends TestCase
{
    use RefreshDatabase;

    protected TradeImpactAnalyzer $analyzer;
    protected TradeIntelligenceSummaryService $summaryService;
    protected CountryPortImpactMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create required test fixtures for Mapper
        Country::create(['country_name' => 'China', 'country_code' => 'CN']);
        Country::create(['country_name' => 'United States of America', 'country_code' => 'US']);
        Country::create(['country_name' => 'United Kingdom', 'country_code' => 'GB']);
        Country::create(['country_name' => 'India', 'country_code' => 'IN']);

        $this->analyzer = new TradeImpactAnalyzer();
        $this->summaryService = new TradeIntelligenceSummaryService();
        $this->mapper = new CountryPortImpactMapper();
    }

    public function test_india_uk_trade_agreement()
    {
        $article = [
            'title' => 'India-UK trade agreement introduces zero-duty access and reduces tariffs',
            'description' => '',
            'content' => '',
            'category' => 'Trade'
        ];
        
        $impact = $this->analyzer->analyze($article);
        
        $this->assertEquals('Decreasing', $impact['risk_direction']);
        $this->assertContains('Trade agreement', $impact['impact_factors']);
        
        $exposure = $this->mapper->map([
            'category' => 'Trade',
            'trade_impact' => $impact,
            'title' => $article['title']
        ])['trade_exposure_type'];
        
        $this->assertEquals('Trade Agreement / Market Access', $exposure);
        
        $summary = $this->summaryService->generate(array_merge($article, ['trade_impact' => $impact]));
        $this->assertStringContainsString('reduce cross-border trade barriers', $summary);
        $this->assertStringNotContainsString('increase cross-border trade costs', $summary);
    }

    public function test_us_tariffs()
    {
        $article = [
            'title' => 'US announces new 25% tariffs on imported steel',
            'description' => '',
            'content' => '',
            'category' => 'Trade'
        ];
        
        $impact = $this->analyzer->analyze($article);
        
        $this->assertEquals('Increasing', $impact['risk_direction']);
        
        $exposure = $this->mapper->map([
            'category' => 'Trade',
            'trade_impact' => $impact,
            'title' => $article['title']
        ])['trade_exposure_type'];
        
        $this->assertEquals('Tariff', $exposure);
    }

    public function test_export_ban_lifted()
    {
        $article = [
            'title' => 'Government removes export ban on rice',
            'description' => '',
            'content' => '',
            'category' => 'Trade'
        ];
        
        $impact = $this->analyzer->analyze($article);
        $this->assertEquals('Decreasing', $impact['risk_direction']);
    }

    public function test_export_ban_imposed()
    {
        $article = [
            'title' => 'Government imposes export ban on rice',
            'description' => '',
            'content' => '',
            'category' => 'Trade'
        ];
        
        $impact = $this->analyzer->analyze($article);
        $this->assertEquals('Increasing', $impact['risk_direction']);
    }

    public function test_sanctions_lifted()
    {
        $article = [
            'title' => 'EU lifts sanctions affecting cross-border trade',
            'description' => '',
            'content' => '',
            'category' => 'Trade'
        ];
        
        $impact = $this->analyzer->analyze($article);
        $this->assertEquals('Decreasing', $impact['risk_direction']);
    }

    public function test_sanctions_imposed()
    {
        $article = [
            'title' => 'EU imposes new sanctions affecting cross-border trade',
            'description' => '',
            'content' => '',
            'category' => 'Trade'
        ];
        
        $impact = $this->analyzer->analyze($article);
        $this->assertEquals('Increasing', $impact['risk_direction']);
    }

    public function test_port_congestion()
    {
        $article = [
            'title' => 'Port congestion causes major shipping delays',
            'description' => '',
            'content' => '',
            'category' => 'Shipping'
        ];
        
        $impact = $this->analyzer->analyze($article);
        $this->assertEquals('Increasing', $impact['risk_direction']);
    }

    public function test_new_trade_agreement()
    {
        $article = [
            'title' => 'New trade agreement expands market access',
            'description' => '',
            'content' => '',
            'category' => 'Trade'
        ];
        
        $impact = $this->analyzer->analyze($article);
        $this->assertEquals('Decreasing', $impact['risk_direction']);
    }

    public function test_stable_negotiations()
    {
        $article = [
            'title' => 'Tariff negotiations continue with no policy change',
            'description' => '',
            'content' => '',
            'category' => 'Trade'
        ];
        
        $impact = $this->analyzer->analyze($article);
        $this->assertEquals('Stable', $impact['risk_direction']);
    }

    public function test_negation_no_new_tariffs() { $this->assertRisk('no new tariffs', 'Stable'); }
    public function test_negation_no_tariff_increase() { $this->assertRisk('no tariff increase', 'Stable'); }
    public function test_negation_tariffs_will_not_increase() { $this->assertRisk('tariffs will not increase', 'Stable'); }
    public function test_negation_will_not_impose_new_tariffs() { $this->assertRisk('government will not impose new tariffs', 'Stable'); }
    public function test_negation_no_new_sanctions() { $this->assertRisk('no new sanctions', 'Stable'); }
    public function test_negation_will_not_impose_sanctions() { $this->assertRisk('government will not impose sanctions', 'Stable'); }
    public function test_negation_no_export_ban() { $this->assertRisk('no export ban', 'Stable'); }
    public function test_negation_will_not_impose_an_export_ban() { $this->assertRisk('government will not impose an export ban', 'Stable'); }
    
    public function test_positive_new_tariffs() { $this->assertRisk('new tariffs', 'Increasing'); }
    public function test_positive_tariffs_increased() { $this->assertRisk('tariffs increased', 'Increasing'); }
    public function test_positive_imposes_new_tariffs() { $this->assertRisk('government imposes new tariffs', 'Increasing'); }
    public function test_positive_new_sanctions_imposed() { $this->assertRisk('new sanctions imposed', 'Increasing'); }
    public function test_positive_export_ban_imposed() { $this->assertRisk('export ban imposed', 'Increasing'); }
    
    public function test_positive_tariffs_reduced() { $this->assertRisk('tariffs reduced', 'Decreasing'); }
    public function test_positive_export_ban_lifted() { $this->assertRisk('export ban lifted', 'Decreasing'); }
    public function test_positive_sanctions_lifted() { $this->assertRisk('sanctions lifted', 'Decreasing'); }

    public function test_edge_case_negation_and_positive() { 
        $this->assertRisk('No new tariffs were announced, but the government imposed an export ban.', 'Increasing'); 
    }
    
    public function test_semantic_noun_only_stable() {
        $this->assertRisk('export ban', 'Stable');
        $this->assertRisk('government discusses export ban', 'Stable');
        $this->assertRisk('sanctions', 'Stable');
        $this->assertRisk('trade restrictions', 'Stable');
    }
    
    public function test_semantic_noun_action_increasing() {
        $this->assertRisk('government imposes export ban', 'Increasing');
        $this->assertRisk('new export ban announced', 'Increasing');
        $this->assertRisk('government extends export ban', 'Increasing');
        $this->assertRisk('export ban extended until year-end', 'Increasing');
        $this->assertRisk('sanctions extended', 'Increasing');
        $this->assertRisk('trade restrictions tightened', 'Increasing');
    }
    
    public function test_semantic_noun_action_decreasing() {
        $this->assertRisk('government lifts export ban', 'Decreasing');
        $this->assertRisk('export ban will be lifted', 'Decreasing');
        $this->assertRisk('government will end export ban', 'Decreasing');
        $this->assertRisk('export ban removed', 'Decreasing');
        $this->assertRisk('trade restrictions eased', 'Decreasing');
    }

    public function test_mixed_case_id13() {
        $this->assertRisk('Moscow extended its gasoline export ban while restrictions on diesel exports will be lifted', 'Stable');
    }
    
    public function test_bounded_context_increasing() {
        $this->assertRisk('extended its gasoline export ban', 'Increasing');
        $this->assertRisk('extended the existing gasoline export ban', 'Increasing');
        $this->assertRisk('government imposed a temporary export ban', 'Increasing');
        $this->assertRisk('government expanded its existing export restrictions', 'Increasing');
    }
    
    public function test_bounded_context_decreasing() {
        $this->assertRisk('government lifted its gasoline export ban', 'Decreasing');
        $this->assertRisk('government removed the temporary export restriction', 'Decreasing');
    }
    
    public function test_bounded_context_stable() {
        $this->assertRisk('gasoline export ban', 'Stable');
        $this->assertRisk('government discusses gasoline export ban', 'Stable');
    }

    protected function assertRisk(string $text, string $expectedDirection)
    {
        $impact = $this->analyzer->analyze(['title' => $text, 'category' => 'Trade']);
        $this->assertEquals($expectedDirection, $impact['risk_direction'], "Failed for text: '$text'");
    }
}
