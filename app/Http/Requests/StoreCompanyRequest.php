<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'company_admin');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:companies,email',
            'user_id' => 'required|exists:users,user_id',
        ];
    }
}
