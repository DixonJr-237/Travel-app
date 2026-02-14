<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id_company,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'agencies_count' => $this->whenLoaded('agencies', function () {
                return $this->agencies->count();
            }),
            'agencies' => AgencyResource::collection($this->whenLoaded('agencies')),
        ];
    }
}
