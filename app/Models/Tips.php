<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tips extends Model
{
    use HasFactory;

    protected $fillable = [
        'departure_date', 'departure_time', 'initial_price',
        'available_seats', 'bus_id', 'journey_id',
        'departure_location_coord_id', 'arrival_location_coord_id',
        'status',
    ];

    protected $table = 'trips';

    protected $primaryKey = 'trip_id';

    protected $dates = ['departure_date'];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            // 'departure_time' => 'datetime', // Optionnel si vous voulez aussi formater l'heure
        ];
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function journey()
    {
        return $this->belongsTo(Journey::class, 'journey_id');
    }

    public function departureLocation()
    {
        return $this->belongsTo(Coordinate::class, 'departure_location_coord_id');
    }

    public function arrivalLocation()
    {
        return $this->belongsTo(Coordinate::class, 'arrival_location_coord_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'trip_id');
    }
}
