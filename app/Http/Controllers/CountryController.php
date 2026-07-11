<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryDetailService;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('country_name')
            ->paginate(20);

        return view('countries.index', compact('countries'));
    }

    public function show(
        Country $country,
        CountryDetailService $service
    )
    {
        return view(
            'countries.show',
            $service->build($country)
        );
    }
}