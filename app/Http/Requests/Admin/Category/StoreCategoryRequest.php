<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCategoryRequest extends FormRequest
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
                'unique:categories,name',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Nama kategori wajib diisi.',

            'name.max' =>
                'Nama kategori maksimal 100 karakter.',

            'name.unique' =>
                'Kategori tersebut sudah tersedia.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
        ]);
    }
}