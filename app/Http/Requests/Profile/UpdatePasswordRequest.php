<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Authorization.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(8),
            ],

            'password_confirmation' => [
                'required',
            ],
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'current_password.required' =>
                'Password saat ini wajib diisi.',

            'current_password.current_password' =>
                'Password saat ini tidak sesuai.',

            'password.required' =>
                'Password baru wajib diisi.',

            'password.confirmed' =>
                'Konfirmasi password tidak cocok.',

            'password.different' =>
                'Password baru harus berbeda dari password saat ini.',

            'password.min' =>
                'Password baru minimal 8 karakter.',

            'password_confirmation.required' =>
                'Konfirmasi password wajib diisi.',
        ];
    }
}