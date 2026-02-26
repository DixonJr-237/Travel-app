<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PhpParser\Node\Stmt\Return_;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has a specific role.
     *
     * @param  string|array  $roles
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }

        return in_array($this->role, $roles);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is a company admin.
     */
    public function isCompanyAdmin(): bool
    {
        return $this->role === 'company_admin';
    }

    /**
     * Check if user is an agency admin.
     */
    public function isAgencyAdmin(): bool
    {
        return $this->role === 'agency_admin';
    }

    /**
     * Check if user is an admin (any type).
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'company_admin', 'agency_admin']);
    }

    // In app/Models/User.php
    public function company()
    {
        // This assumes companies table has a 'user_id' column
        return $this->hasOne(Company::class, 'user_id');
    }

    public function agence()
    {
        return $this->hasOne(Agence::class,'user_id');
    }



}
