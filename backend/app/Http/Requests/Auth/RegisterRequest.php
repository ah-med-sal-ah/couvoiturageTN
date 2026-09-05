<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'profile_photo' => ['nullable', 'image', 'max:8192'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'cin' => ['required', 'string', 'regex:/^[0-9]{8}$/', 'unique:users,cin'],
            'age' => ['required', 'integer', 'min:16', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_.]+$/', 'unique:users,username'],
            'password' => ['required', 'confirmed', Password::min(9)->mixedCase()->numbers()->symbols()],
            'gender' => ['required', 'in:female,male'],
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
            'password.confirmed' => 'The password confirmation does not match.',
            'profile_photo.image' => 'The profile photo must be a valid image file (JPG, PNG, GIF, WEBP, HEIC).',
            'profile_photo.max' => 'The profile photo must not be larger than 8 MB.',
            // Fires when PHP itself rejects the upload before Laravel's own
            // rules even run - typically the server's `upload_max_filesize`/
            // `post_max_size` ini limits are lower than this form allows.
            'profile_photo.uploaded' => 'The profile photo could not be uploaded. Please try a smaller photo or a different network connection.',
        ];
    }
}
