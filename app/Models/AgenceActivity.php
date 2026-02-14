<?php

namespace App\Models;

use Faker\Provider\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgenceActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_company', 'id_agency', 'id_region', 'id_coord',
    ];

    protected $table = 'agency_activities';

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    public function agency()
    {
        return $this->belongsTo(Agence::class, 'id_agency');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region');
    }
}
