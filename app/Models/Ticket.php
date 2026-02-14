<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_date', 'price', 'status',
        'journey_id', 'customer_id', 'trip_id',
        'seat_number', 'booking_reference'
    ];

    protected $primaryKey = 'ticket_id';

    protected $dates = ['purchase_date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function journey()
    {
        return $this->belongsTo(Journey::class, 'journey_id');
    }

    public function trip()
    {
        return $this->belongsTo(Tips::class, 'trip_id');
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class, 'ticket_id');
    }
}
