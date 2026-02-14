<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'id_country'];
    protected $primaryKey = 'id_region';

    public function country()
    {
        return $this->belongsTo(Country::class, 'id_country');
    }

    public function subRegions()
    {
        return $this->hasMany(SubRegion::class, 'id_region');
    }
}
