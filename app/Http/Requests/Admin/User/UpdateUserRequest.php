<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check()
            && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'min:5',
                'max:50',
                Rule::unique('users', 'username')
                    ->ignore($this->route('user')),
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:20',
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'barista',
                ]),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.min' => 'Username minimal 5 karakter',

            'phone_number.regex' => 'Nomor HP harus diawali 62 tanpa tanda + dan hanya berisi angka.',
            'phone_number.max' => 'Nomor HP maksimal 14 digit.',

            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',

            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
