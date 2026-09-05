<?php

namespace App\Http\Requests\Publication;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:passenger,driver'],
            'departure_location_id' => ['required', 'integer', 'exists:locations,id'],
            'arrival_location_id' => ['required', 'integer', 'exists:locations,id', 'different:departure_location_id'],
            'available_seats' => ['required', 'integer', 'min:1', 'max:8'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_time' => ['required', 'date_format:H:i'],
            'phone' => ['required', 'string', 'regex:/^(\+216)?[0-9]{8}$/'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'arrival_location_id.different' => 'The arrival point must be different from the departure point.',
            'phone.regex' => 'Enter a valid Tunisian phone number (8 digits, optionally prefixed with +216).',
            'departure_date.after_or_equal' => 'The departure date cannot be in the past.',
        ];
    }
}
