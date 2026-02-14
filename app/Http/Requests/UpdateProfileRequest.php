<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        $userId = Auth::id();

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId . ',user_id',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
