<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicData extends Model
{

    protected $fillable = [

        'country_id',

        'gdp',

        'inflation',

        'exports',

        'imports',

        'data_year'

    ];

        protected $casts = [

        'gdp' => 'float',

        'inflation' => 'float',

        'exports' => 'float',

        'imports' => 'float',

    ];

    public function country()
    {

        return $this->belongsTo(
            Country::class
        );

    }

    public function getFormattedGdpAttribute()
    {
        $gdp = $this->gdp;

        if (!$gdp) {
            return '-';
        }

        if ($gdp >= 1000000000000) {
            return '$' . number_format($gdp / 1000000000000, 2) . ' T';
        }

        return '$' . number_format($gdp / 1000000000, 0) . ' B';
    }

        public function getFormattedExportsAttribute()
    {
        if (!$this->exports) {
            return '-';
        }

        return '$' . number_format($this->exports / 1000000000, 2) . ' B';
    }

    public function getFormattedImportsAttribute()
    {
        if (!$this->imports) {
            return '-';
        }

        return '$' . number_format($this->imports / 1000000000, 2) . ' B';
    }

    public function getInflationColorAttribute()
    {
        if ($this->inflation <= 2) {
            return 'success';
        }

        if ($this->inflation <= 5) {
            return 'warning';
        }

        return 'danger';
    }

}