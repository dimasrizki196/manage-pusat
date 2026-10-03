<?php

namespace App\Http\Requests\Barista\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomingTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'barista';
    }

    public function rules(): array
    {
        return [
            'transaction_source' => [
                'required',
                'in:pengajuan,urgent',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'restock_request_id' => [
                'nullable',
                'integer',
                'exists:restock_requests,id',
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
            'transaction_source.required' => 'Sumber barang masuk wajib dipilih.',
            'transaction_source.in' => 'Sumber barang masuk tidak valid.',
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.integer' => 'Barang tidak valid.',
            'item_id.exists' => 'Barang yang dipilih tidak ditemukan.',
            'restock_request_id.integer' => 'Pengajuan tidak valid.',
            'restock_request_id.exists' => 'Pengajuan tidak ditemukan.',
            'quantity.required' => 'Jumlah barang wajib diisi.',
            'quantity.numeric' => 'Jumlah barang harus berupa angka.',
            'quantity.gt' => 'Jumlah barang harus lebih dari 0.',
        ];
    }
}
