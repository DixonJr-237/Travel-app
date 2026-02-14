<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agence extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agencies';
    protected $primaryKey = 'id_agence';

    protected $fillable = [
        'name',
        'user_id',
        'id_company',
        'id_coord',
        'id_city',
        'phone',
        'email',
        'address',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the company that owns the agency.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company', 'id_company');
    }

    /**
     * Get the user (agency admin) that manages the agency.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the coordinates of the agency.
     */
    public function coordinates()
    {
        return $this->belongsTo(Coordinate::class, 'id_coord', 'id_coord');
    }

    /**
     * Get the city where the agency is located.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'id_city', 'id_city');
    }

    /**
     * Get the buses belonging to this agency.
     */
    public function buses()
    {
        return $this->hasMany(Bus::class, 'agency_id', 'id_agence');
    }

    /**
     * Get the users belonging to this agency.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'agency_id', 'id_agence');
    }

    /**
     * Get the activities for this agency.
     */
    public function activities()
    {
        return $this->hasMany(AgenceActivity::class, 'id_agency', 'id_agence');
    }

    /**
     * Scope a query to only include active agencies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending agencies.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
