<?php

namespace App\Http\Requests\Barista\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutgoingTransactionRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.integer' => 'Barang tidak valid.',
            'item_id.exists' => 'Barang yang dipilih tidak ditemukan.',
            'quantity.required' => 'Jumlah barang wajib diisi.',
            'quantity.numeric' => 'Jumlah barang harus berupa angka.',
            'quantity.gt' => 'Jumlah barang harus lebih dari 0.',
        ];
    }
}
