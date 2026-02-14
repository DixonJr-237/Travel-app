<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number', 'seats_count', 'agency_id',
        'model', 'year', 'status'
    ];

    protected $primaryKey = 'bus_id';

    public function agency()
    {
        return $this->belongsTo(Agence::class, 'agency_id');
    }

    public function trips()
    {
        return $this->hasMany(Tips::class, 'bus_id');
    }
}
