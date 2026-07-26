<?php

namespace App\Services\News;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Support\Str;

class CountryPortImpactMapper
{
    protected array $countryAliasMap = [
        'us' => 'United States of America',
        'usa' => 'United States of America',
        'united states' => 'United States of America',
        'uk' => 'United Kingdom',
        'britain' => 'United Kingdom',
        'great britain' => 'United Kingdom',
        'china' => 'China',
        'prc' => 'China',
        'singapore' => 'Singapore',
    ];

    protected array $regionalEntities = [
        'eu' => 'European Union',
        'european union' => 'European Union',
        'europe' => 'Europe',
        'asia' => 'Asia',
        'middle east' => 'Middle East',
        'africa' => 'Africa',
        'north america' => 'North America',
        'south america' => 'South America',
        'latin america' => 'Latin America',
        'asean' => 'ASEAN',
    ];

    /**
     * Maps news article metadata to exact Country and Port records.
     */
    public function map(array $articleData): array
    {
        $mappedCountries = [];
        $mappedRegionalEntities = [];
        $mappedPorts = [];
        $portImpactType = 'NONE';
        
        $impactData = $articleData['trade_impact'] ?? [];
        if (empty($impactData) && isset($articleData['affected_countries'])) {
            $impactData = $articleData;
        }

        $affectedCountries = $impactData['affected_countries'] ?? [];
        $category = $articleData['category'] ?? 'General';
        $impactFactors = $impactData['impact_factors'] ?? [];
        
        $contentToSearch = strtolower(($articleData['title'] ?? '') . ' ' . ($articleData['description'] ?? '') . ' ' . ($articleData['content'] ?? ''));

        // 1. Resolve Countries & Regions
        foreach ($affectedCountries as $countryString) {
            $normalizedName = $this->normalizeEntityName($countryString);
            
            // Check if it's a regional entity first
            $regionName = $this->resolveRegionalEntity($normalizedName);
            if ($regionName) {
                $mappedRegionalEntities[] = [
                    'name' => $regionName,
                    'exposure' => 'Regional',
                    'confidence' => 0.90
                ];
                continue;
            }

            // Check if it's a country
            $countryRecord = $this->resolveCountry($normalizedName);
            if ($countryRecord) {
                $mappedCountries[] = [
                    'country_id' => $countryRecord->id,
                    'country_code' => $countryRecord->country_code,
                    'name' => $countryRecord->country_name,
                    'exposure' => 'Direct',
                    'confidence' => 0.95
                ];
            }
        }

        // 2. Resolve Trade Exposure Type
        $tradeExposureType = $this->resolveTradeExposureType($category, $impactFactors, $contentToSearch);

        // 3. Resolve Ports
        $portMapping = $this->resolvePorts($contentToSearch, $mappedCountries);
        $mappedPorts = $portMapping['ports'];
        
        // Determine Port Impact Type
        if (!empty($mappedPorts)) {
            $portImpactType = 'DIRECT';
        } elseif (!empty($mappedCountries) && (in_array($category, ['Shipping', 'Logistics']) || in_array($tradeExposureType, ['Shipping', 'Port Operations', 'Logistics']))) {
            // No explicit port found, but category implies country-level shipping exposure
            $portImpactType = 'COUNTRY_LEVEL';
        }

        // 5. Calculate overall confidence
        $confidence = $this->calculateOverallConfidence($mappedCountries, $mappedRegionalEntities, $mappedPorts);

        return [
            'mapped_countries' => $mappedCountries,
            'regional_entities' => $mappedRegionalEntities,
            'mapped_ports' => $mappedPorts,
            'port_impact_type' => $portImpactType,
            'trade_exposure_type' => $tradeExposureType,
            'mapping_confidence' => $confidence,
        ];
    }

    protected function normalizeEntityName(string $name): string
    {
        return strtolower(trim($name));
    }

    protected function resolveRegionalEntity(string $normalizedName): ?string
    {
        if (isset($this->regionalEntities[$normalizedName])) {
            return $this->regionalEntities[$normalizedName];
        }

        foreach ($this->regionalEntities as $key => $region) {
            if (str_contains($normalizedName, $key)) {
                return $region;
            }
        }
        return null;
    }

    protected function resolveCountry(string $normalizedName): ?Country
    {
        // Check alias map
        if (isset($this->countryAliasMap[$normalizedName])) {
            $resolvedName = $this->countryAliasMap[$normalizedName];
            return Country::where('country_name', $resolvedName)->first();
        }

        // Try exact match
        $exactMatch = Country::whereRaw('LOWER(country_name) = ?', [$normalizedName])->first();
        if ($exactMatch) return $exactMatch;

        // Try code match
        $codeMatch = Country::whereRaw('LOWER(country_code) = ?', [$normalizedName])->first();
        if ($codeMatch) return $codeMatch;

        // Try LIKE match safely
        $likeMatch = Country::whereRaw('LOWER(country_name) LIKE ?', ["%{$normalizedName}%"])->first();
        if ($likeMatch) return $likeMatch;

        return null;
    }

    protected function resolveTradeExposureType(string $category, array $impactFactors, string $contentToSearch): string
    {
        // Check exact impact factors first
        if (in_array('Trade agreement', $impactFactors)) return 'Trade Agreement / Market Access';
        if (in_array('Export restriction', $impactFactors)) return 'Export Restriction';
        if (in_array('Sanctions', $impactFactors) || in_array('Trade restriction', $impactFactors)) return 'Sanctions / Trade Restriction';
        if (in_array('Technology supply', $impactFactors)) return 'Technology Supply / Semiconductor';
        
        $factorStr = strtolower(implode(' ', $impactFactors));
        
        if (str_contains($factorStr, 'tariff') || (in_array('Tariff change', $impactFactors))) return 'Tariff';
        if (str_contains($factorStr, 'export')) return 'Export';
        if (str_contains($factorStr, 'import')) return 'Import';
        if (str_contains($factorStr, 'freight') || in_array('Freight rate movement', $impactFactors)) return 'Freight';
        if (str_contains($factorStr, 'congestion') || str_contains($factorStr, 'port')) return 'Port Operations';
        
        return match($category) {
            'Trade' => 'Trade Policy',
            'Shipping' => 'Shipping',
            'Logistics' => 'Logistics',
            'Manufacturing' => 'Manufacturing',
            'Energy' => 'Energy Trade',
            'Technology' => 'Technology Trade',
            default => 'Supply Chain',
        };
    }

    protected function resolvePorts(string $contentToSearch, array $mappedCountries): array
    {
        $mappedPorts = [];
        
        // Strict anti-hallucination check for false positives
        if (str_contains($contentToSearch, 'usb port') || str_contains($contentToSearch, 'airport terminal') || str_contains($contentToSearch, 'computer terminal')) {
            // Strip out these phrases before regex matching
            $contentToSearch = str_replace(['usb port', 'airport terminal', 'computer terminal'], '', $contentToSearch);
        }

        // Only map if explicitly identified as a maritime port/terminal
        // Match next 1-2 words to prevent matching entire sentences
        preg_match_all('/(?:port of|port|terminal|seaport)\s+([a-z]+(?:\s+[a-z]+)?)/i', $contentToSearch, $matchesAfter);
        preg_match_all('/([a-z]+(?:\s+[a-z]+)?)\s+(?:port|terminal|seaport)/i', $contentToSearch, $matchesBefore);

        $potentialPortNames = array_merge($matchesAfter[1], $matchesBefore[1]);
        $potentialPortNames = array_map('trim', $potentialPortNames);
        
        $countryCodes = array_column($mappedCountries, 'country_code');
        $countryNames = array_map('strtolower', array_column($mappedCountries, 'name'));

        foreach ($potentialPortNames as $potentialName) {
            $words = explode(' ', $potentialName);
            $candidates = [$potentialName];
            if (count($words) > 1) {
                $candidates[] = $words[0]; // Try just the first word if two words fail
            }

            foreach ($candidates as $candidate) {
                if (strlen($candidate) < 3) continue;
                $candidateLower = strtolower($candidate);

                $query = Port::whereRaw('LOWER(name) LIKE ?', ["%{$candidateLower}%"]);
                
                if (!empty($countryCodes)) {
                    $query->whereIn('country_iso2', $countryCodes);
                }

                $ports = $query->get();

                if ($ports->count() > 0) {
                    $matchedPort = null;
                    $confidence = 0.60;

                    // Evaluate matches to prevent generic geographic mapping
                    foreach ($ports as $port) {
                        $portNameLower = strtolower($port->name);
                        
                        // Exact match is best
                        if ($portNameLower === $candidateLower) {
                            $matchedPort = $port;
                            $confidence = 0.95;
                            break;
                        }
                        
                        // If the candidate is just the country name or region, it's too generic
                        if (in_array($candidateLower, $countryNames) || $candidateLower === strtolower($port->country_name) || $candidateLower === strtolower($port->city)) {
                            // Only allow it if it's an exact match or we have NO other ports in this city/country, 
                            // but actually Rule 1 says: "DO NOT arbitrarily select one... Generic geographic port phrase: DO NOT create a DIRECT port mapping."
                            continue;
                        }

                        // Strong alias match (e.g. candidate is "Keppel", port is "Keppel - (east Singapore)")
                        if (str_starts_with($portNameLower, $candidateLower) || str_ends_with($portNameLower, $candidateLower) || str_contains($portNameLower, $candidateLower)) {
                            $matchedPort = $port;
                            $confidence = 0.85;
                            // Don't break, keep looking for exact match
                        }
                    }

                    if ($matchedPort) {
                        $exists = false;
                        foreach ($mappedPorts as $mp) {
                            if ($mp['port_id'] == $matchedPort->id) $exists = true;
                        }
                        
                        if (!$exists) {
                            $mappedPorts[] = [
                                'port_id' => $matchedPort->id,
                                'port_code' => $matchedPort->code,
                                'name' => $matchedPort->name,
                                'confidence' => $confidence
                            ];
                        }
                        break; // Stop trying candidates once a port is found
                    }
                }
            }
        }
        
        return ['ports' => $mappedPorts];
    }

    protected function calculateOverallConfidence(array $countries, array $regions, array $ports): float
    {
        if (!empty($ports)) return 0.95;
        if (!empty($countries)) return 0.90;
        if (!empty($regions)) return 0.80;
        return 0.50; // Unknown or weak mapping
    }
}
