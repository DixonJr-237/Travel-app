<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubRegion extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'id_region'];
    protected $primaryKey = 'id_sub_region';

    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'id_sub_region');
    }
}
