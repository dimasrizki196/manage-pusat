<?php

namespace App\Http\Requests\Admin\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],

            'transaction_type' => [
                'required',
                Rule::in([
                    'masuk',
                    'keluar',
                ]),
            ],

            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'item_id' => 'barang',
            'transaction_type' => 'jenis transaksi',
            'quantity' => 'jumlah',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.exists' => 'Barang yang dipilih tidak ditemukan.',
            'transaction_type.required' => 'Jenis transaksi wajib dipilih.',
            'transaction_type.in' => 'Jenis transaksi tidak valid.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.gt' => 'Jumlah harus lebih dari 0.',
        ];
    }
}
