<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreTripRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && in_array(auth()->user()->role, ['super_admin', 'company_admin', 'agency_admin']);
    }

    public function rules()
    {
        return [
            'departure_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
            'initial_price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:1',
            'bus_id' => 'required|exists:buses,bus_id',
            'journey_id' => 'required|exists:journeys,journey_id',
            'departure_location_coord_id' => 'required|exists:coordinates,id_coord',
            'arrival_location_coord_id' => 'required|exists:coordinates,id_coord',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->departure_date && $this->departure_time) {
                $departureDateTime = Carbon::parse($this->departure_date.' '.$this->departure_time);
                if ($departureDateTime->isPast()) {
                    $validator->errors()->add('departure_date', 'The departure date and time must be in the future.');
                }
            }
        });
    }
}
