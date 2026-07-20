<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchList extends Model
{
    protected $table = 'watchlists'; // Since Laravel might look for watch_lists by default

    protected $fillable = [
        'user_id',
        'watch_type',
        'country_id',
        'port_id',
        'route_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function port()
    {
        return $this->belongsTo(Port::class);
    }

    public function route()
    {
        return $this->belongsTo(ShippingRoute::class, 'route_id');
    }
}
