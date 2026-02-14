<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->ticket_id,
            'booking_reference' => $this->booking_reference,
            'purchase_date' => $this->purchase_date->format('Y-m-d H:i:s'),
            'price' => (float) $this->price,
            'status' => $this->status,
            'seat_number' => $this->seat_number,
            'customer' => [
                'id' => $this->customer->customer_id,
                'name' => $this->customer->first_name.' '.$this->customer->last_name,
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
            ],
            'trip' => new TripResource($this->whenLoaded('trip')),
            'journey' => [
                'name' => $this->journey->name,
            ],
            'reservation' => $this->whenLoaded('reservation', function () {
                return [
                    'id' => $this->reservation->reservation_id,
                    'date' => $this->reservation->date->format('Y-m-d'),
                    'status' => $this->reservation->status,
                ];
            }),
            'qr_code_url' => route('tickets.qr', $this->booking_reference),
            'print_url' => route('tickets.print', $this->ticket_id),
        ];
    }
}
