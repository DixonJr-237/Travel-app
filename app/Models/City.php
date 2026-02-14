<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'id_sub_region'];
    protected $primaryKey = 'id_city';

    public function subRegion()
    {
        return $this->belongsTo(SubRegion::class, 'id_sub_region');
    }

    public function agencies()
    {
        return $this->hasMany(Agence::class, 'id_city');
    }

    public function coordinates()
    {
        return $this->hasMany(Coordinate::class, 'id_city');
    }
}
