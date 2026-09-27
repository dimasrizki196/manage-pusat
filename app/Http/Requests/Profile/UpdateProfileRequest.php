<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    /**
     * User hanya boleh mengupdate profile miliknya sendiri.
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
        $user = $this->user();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')
                    ->ignore($user->id),
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:20',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' =>
                'Nama wajib diisi.',

            'name.max' =>
                'Nama maksimal 255 karakter.',

            'username.required' =>
                'Username wajib diisi.',

            'username.max' =>
                'Username maksimal 50 karakter.',

            'username.unique' =>
                'Username tersebut sudah digunakan.',

            'phone_number.max' =>
                'Nomor WhatsApp maksimal 20 karakter.',
        ];
    }

    /**
     * Normalize input.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => strtolower(
                trim($this->username ?? '')
            ),

            'name' => trim(
                $this->name ?? ''
            ),

            'phone_number' => $this->phone_number
                ? trim($this->phone_number)
                : null,
        ]);
    }
}