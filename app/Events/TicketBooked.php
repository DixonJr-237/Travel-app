<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketBooked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function broadcastOn()
    {
        $channels = [];

        // Notify agency admin
        if ($this->ticket->trip->bus->agency) {
            $channels[] = new PrivateChannel('agency.' . $this->ticket->trip->bus->agency->id_agence);
        }

        // Notify company admin
        if ($this->ticket->trip->bus->agency->company) {
            $channels[] = new PrivateChannel('company.' . $this->ticket->trip->bus->agency->company->id_company);
        }

        return $channels;
    }

    public function broadcastWith()
    {
        return [
            'ticket_id' => $this->ticket->ticket_id,
            'booking_reference' => $this->ticket->booking_reference,
            'customer_name' => $this->ticket->customer->full_name,
            'trip_details' => [
                'from' => $this->ticket->trip->departureLocation->city->name,
                'to' => $this->ticket->trip->arrivalLocation->city->name,
                'date' => $this->ticket->trip->departure_date,
                'time' => $this->ticket->trip->departure_time,
            ],
            'price' => $this->ticket->price,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
