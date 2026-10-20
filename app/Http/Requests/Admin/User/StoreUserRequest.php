<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreUserRequest extends FormRequest
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
                'max:100',
            ],

            'username' => [
                'required',
                'string',
                'min:5',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username'),
            ],

            'phone_number' => [
                'nullable',
                'string',
                'regex:/^628[0-9]{8,11}$/',
                'max:14',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'barista',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
            'Nama wajib diisi.',

            'username.required' =>
            'Username wajib diisi.',

            'username.unique' =>
            'Username sudah digunakan.',

            'username.alpha_dash' =>
            'Username hanya boleh menggunakan huruf, angka, dash, dan underscore.',

            'username.min' => 'Username minimal 5 karakter',

            'phone_number.regex' => 'Nomor HP harus diawali 62 tanpa + dan hanya berisi angka.',
            'phone_number.max' => 'Nomor HP maksimal 14 digit.',

            'password.min' =>
            'Password minimal 8 karakter.',

            'password.confirmed' =>
            'Konfirmasi password tidak cocok.',

            'role.required' =>
            'Role wajib dipilih.',

            'role.in' =>
            'Role yang dipilih tidak valid.',
        ];
    }
}
