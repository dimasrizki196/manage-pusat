<?php

namespace App\Http\Requests\Admin\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'unit_id' => [
                'required',
                'integer',
                Rule::exists('units', 'id'),
            ],

            'stock_min' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori barang tidak ditemukan.',

            'name.required' => 'Nama barang wajib diisi.',
            'name.max' => 'Nama barang maksimal 255 karakter.',

            'unit_id.required' => 'Satuan wajib dipilih.',
            'unit_id.exists' => 'Satuan tidak ditemukan.',

            'stock_min.required' => 'Stok minimum wajib diisi.',
            'stock_min.numeric' => 'Stok minimum harus berupa angka.',
            'stock_min.min' => 'Stok minimum tidak boleh kurang dari 0.',
        ];
    }
}