<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $userId = $this->user()->id;

        return [
            'profile_photo' => ['nullable', 'image', 'max:8192'],
            'remove_profile_photo' => ['sometimes', 'boolean'],
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'cin' => ['sometimes', 'required', 'string', 'regex:/^[0-9]{8}$/', Rule::unique('users', 'cin')->ignore($userId)],
            'age' => ['sometimes', 'required', 'integer', 'min:16', 'max:120'],
            'username' => ['sometimes', 'required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_.]+$/', Rule::unique('users', 'username')->ignore($userId)],
            'gender' => ['sometimes', 'required', 'in:female,male'],
            'language' => ['sometimes', 'required', 'in:en,fr,ar'],
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
            'cin.regex' => 'The CIN must be exactly 8 digits.',
            'username.regex' => 'The username may only contain letters, numbers, dots and underscores.',
            'cin.unique' => 'An account with this CIN already exists.',
            'username.unique' => 'This username is already taken.',
            'profile_photo.image' => 'The profile photo must be a valid image file (JPG, PNG, GIF, WEBP, HEIC).',
            'profile_photo.max' => 'The profile photo must not be larger than 8 MB.',
            'profile_photo.uploaded' => 'The profile photo could not be uploaded. Please try a smaller photo or a different network connection.',
        ];
    }
}
