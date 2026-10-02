<?php

namespace App\Http\Requests\Barista\RestockRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'barista';
    }

    public function rules(): array
    {
        return [
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.integer' => 'Barang tidak valid.',
            'item_id.exists' => 'Barang yang dipilih tidak ditemukan.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.gt' => 'Jumlah harus lebih dari 0.',
            'note.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
