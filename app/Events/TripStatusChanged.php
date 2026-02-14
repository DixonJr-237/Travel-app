<?php

namespace App\Events;

use App\Models\Tips;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;
    public $oldStatus;
    public $newStatus;

    public function __construct(Tips $trip, $oldStatus, $newStatus)
    {
        $this->trip = $trip;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn()
    {
        $channels = [new Channel('trips')];

        // Notify specific agency
        if ($this->trip->bus->agency) {
            $channels[] = new PrivateChannel('agency.' . $this->trip->bus->agency->id_agence);
        }

        // Notify customers with tickets
        foreach ($this->trip->tickets as $ticket) {
            if ($ticket->customer && $ticket->customer->user) {
                $channels[] = new PrivateChannel('user.' . $ticket->customer->user->user_id);
            }
        }

        return $channels;
    }

    public function broadcastWith()
    {
        return [
            'trip_id' => $this->trip->trip_id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'trip_details' => [
                'from' => $this->trip->departureLocation->city->name,
                'to' => $this->trip->arrivalLocation->city->name,
                'date' => $this->trip->departure_date,
                'time' => $this->trip->departure_time,
            ],
            'message' => "Trip status changed from {$this->oldStatus} to {$this->newStatus}",
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
