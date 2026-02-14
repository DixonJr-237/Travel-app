<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'status', 'ticket_id', 'reservation_code', 'created_at', 'updated_at'
    ];

    protected $primaryKey = 'reservation_id';

    protected $dates = ['date'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
