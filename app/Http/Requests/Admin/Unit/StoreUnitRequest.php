<?php

namespace App\Http\Requests\Admin\Unit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUnitRequest extends FormRequest
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
                'unique:units,name',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
            'Nama unit wajib diisi.',

            'name.max' =>
            'Nama unit maksimal 100 karakter.',

            'name.unique' =>
            'Unit tersebut sudah tersedia.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
        ]);
    }
}
