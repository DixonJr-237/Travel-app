<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkAgencyActionRequest extends FormRequest
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
            'agency_ids' => 'required|array',
            'agency_ids.*' => 'integer|exists:agencies,id_agence',
            'action' => 'required|string|in:activate,deactivate,delete',
            'reason' => 'required_if:action,suspend|string|max:500|nullable',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'agency_ids.required' => 'Please select at least one agency.',
            'agency_ids.array' => 'Invalid agency selection.',
            'agency_ids.*.exists' => 'One or more selected agencies do not exist.',
            'action.required' => 'Please specify an action.',
            'action.in' => 'Invalid action. Allowed actions: activate, deactivate, delete.',
            'reason.required_if' => 'Please provide a reason for suspension.',
            'reason.max' => 'The suspension reason cannot exceed 500 characters.',
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
                ->with('error', 'Bulk action failed. Please correct the errors.')
        );
    }
}
