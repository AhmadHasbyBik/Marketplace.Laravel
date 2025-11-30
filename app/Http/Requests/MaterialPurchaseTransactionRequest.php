<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialPurchaseTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string'],
            'transaction_material_purchase_id' => ['nullable', 'exists:material_purchases,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.gt' => 'Jumlah transaksi harus bernilai positif.',
        ];
    }
}
