<?php

namespace App\Contracts;

interface NewsProviderInterface
{
    public function fetch();
    public function fetchLatest();
    public function fetchBusiness();
    public function fetchTrade();
    public function fetchEconomy();
    public function fetchByCategory(string $category);
    public function fetchByCountry(string $countryCode);
    public function fetchEverything();
    public function healthCheck(): bool;
}
