<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id_agence,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'location' => [
                'address' => $this->coordinates->address ?? null,
                'city' => $this->city->name ?? null,
                'coordinates' => [
                    'latitude' => $this->coordinates->latitude ?? null,
                    'longitude' => $this->coordinates->longitude ?? null,
                ],
            ],
            'company' => new CompanyResource($this->whenLoaded('company')),
            'statistics' => $this->when($request->has('with_stats'), function () {
                return [
                    'buses_count' => $this->buses->count(),
                    'active_trips' => $this->getActiveTripsCount(),
                    'monthly_revenue' => $this->getMonthlyRevenue(),
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
