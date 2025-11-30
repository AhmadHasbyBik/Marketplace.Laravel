<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn ($item) => ! empty($item['material_id']) && ($item['quantity'] ?? '') !== '' && ($item['unit_cost'] ?? '') !== '')
            ->map(fn ($item) => [
                'material_id' => $item['material_id'],
                'quantity' => $item['quantity'] ?? null,
                'unit_cost' => $item['unit_cost'] ?? null,
            ])
            ->values()
            ->all();

        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', 'in:draft,ordered,received'],
            'items' => ['required', 'array', 'min:1'],
             'items.*.material_id' => ['required', 'exists:materials,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Tambahkan minimal satu bahan',
            'items.min' => 'Tambahkan minimal satu bahan',
        ];
    }
}
