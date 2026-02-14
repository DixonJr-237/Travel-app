<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coordinate extends Model
{
    use HasFactory;

    protected $fillable = [
        'geo_coord', 'id_agency', 'id_city',
        'latitude', 'longitude', 'address'
    ];

    protected $primaryKey = 'id_coord';

    public function agency()
    {
        return $this->belongsTo(Agence::class, 'id_agency');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'id_city');
    }

    public function departureJourneys()
    {
        return $this->hasMany(Journey::class, 'departure_location_coord_id');
    }

    public function arrivalJourneys()
    {
        return $this->hasMany(Journey::class, 'arrival_location_coord_id');
    }
}
