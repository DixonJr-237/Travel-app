<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Journey extends Model
{
    use HasFactory;

    protected $fillable = [
        'departure_location_coord_id', 'arrival_location_coord_id',
        'distance', 'estimated_duration', 'name'
    ];

    protected $primaryKey = 'journey_id';

    public function departureLocation()
    {
        return $this->belongsTo(Coordinate::class, 'departure_location_coord_id');
    }

    public function arrivalLocation()
    {
        return $this->belongsTo(Coordinate::class, 'arrival_location_coord_id');
    }

    public function trips()
    {
        return $this->hasMany(Tips::class, 'journey_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'journey_id');
    }
}
