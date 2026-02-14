<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->trip_id,
            'departure_date' => $this->departure_date->format('Y-m-d'),
            'departure_time' => $this->departure_time,
            'initial_price' => (float) $this->initial_price,
            'available_seats' => $this->available_seats,
            'status' => $this->status,
            'journey' => [
                'id' => $this->journey->journey_id,
                'name' => $this->journey->name,
                'distance' => (float) $this->journey->distance,
                'estimated_duration' => $this->journey->estimated_duration,
            ],
            'bus' => [
                'id' => $this->bus->bus_id,
                'registration_number' => $this->bus->registration_number,
                'seats_count' => $this->bus->seats_count,
                'model' => $this->bus->model,
                'agency' => [
                    'id' => $this->bus->agency->id_agence,
                    'name' => $this->bus->agency->name,
                ],
            ],
            'departure_location' => [
                'city' => $this->departureLocation->city->name,
                'address' => $this->departureLocation->address,
                'coordinates' => [
                    'latitude' => $this->departureLocation->latitude,
                    'longitude' => $this->departureLocation->longitude,
                ],
            ],
            'arrival_location' => [
                'city' => $this->arrivalLocation->city->name,
                'address' => $this->arrivalLocation->address,
                'coordinates' => [
                    'latitude' => $this->arrivalLocation->latitude,
                    'longitude' => $this->arrivalLocation->longitude,
                ],
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
