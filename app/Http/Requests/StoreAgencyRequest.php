<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAgencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() &&
               (auth()->user()->role === 'super_admin' ||
                auth()->user()->role === 'company_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Agency Information
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:agencies,email',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]*$/',

            // Location
            'id_company' => 'required|exists:companies,id_company',
            'id_city' => 'required|exists:cities,id_city',
            'id_coord' => 'required|exists:coordinates,id_coord',

            // Admin User Information
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The agency name is required.',
            'name.max' => 'The agency name cannot exceed 255 characters.',
            'email.required' => 'The agency email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered for another agency.',
            'phone.required' => 'The phone number is required.',
            'phone.regex' => 'Please enter a valid phone number.',
            'id_company.required' => 'Please select a company.',
            'id_company.exists' => 'The selected company does not exist.',
            'id_city.required' => 'Please select a city.',
            'id_city.exists' => 'The selected city does not exist.',
            'id_coord.required' => 'Please select a location/coordinates.',
            'id_coord.exists' => 'The selected location does not exist.',
            'admin_name.required' => 'The admin name is required.',
            'admin_email.required' => 'The admin email is required.',
            'admin_email.unique' => 'This admin email is already registered.',
            'admin_password.required' => 'The admin password is required.',
            'admin_password.min' => 'The admin password must be at least 8 characters.',
            'admin_password.confirmed' => 'The admin password confirmation does not match.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.')
        );
    }
}
