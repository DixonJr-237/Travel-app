<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'user_id',
    ];

    protected $primaryKey = 'id_company';

    // In app/Models/Company.php
    public function users()
    {
        return $this->hasMany(User::class, 'company_id', 'id_company');
        //                      foreign key  , local key
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the agencies for this company.
     */
    public function agencies()
    {
        return $this->hasMany(Agence::class, 'id_company', 'id_company');
    }


}
