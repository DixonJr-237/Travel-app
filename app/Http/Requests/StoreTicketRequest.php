<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Trip;

class StoreTicketRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'trip_id' => 'required|exists:trips,trip_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'seat_number' => 'required|string',
            'quantity' => 'required|integer|min:1|max:10',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->trip_id) {
                $trip = Trip::find($this->trip_id);
                if ($trip && $trip->available_seats < $this->quantity) {
                    $validator->errors()->add('quantity', 'Not enough seats available.');
                }
                if ($trip && $trip->status !== 'scheduled') {
                    $validator->errors()->add('trip_id', 'This trip is not available for booking.');
                }
            }
        });
    }
}
