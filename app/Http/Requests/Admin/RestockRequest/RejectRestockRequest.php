<?php

namespace App\Http\Requests\Admin\RestockRequest;

use Illuminate\Foundation\Http\FormRequest;

class RejectRestockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}