<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'message', 'status', 'created_at', 'title', 'is_read',
        'user_id', 'type', 'data'
    ];

    protected $primaryKey = 'notification_id';

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
