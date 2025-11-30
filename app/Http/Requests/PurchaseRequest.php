<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation by removing empty item rows
     * so blank template rows do not accidentally fail validation.
     */
    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(function ($item) {
                return isset($item['product_id'], $item['quantity'], $item['unit_cost']) &&
                    $item['product_id'] !== '' &&
                    $item['quantity'] !== '' &&
                    $item['unit_cost'] !== '';
            })
            ->map(fn ($item) => [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
            ])
            ->values()
            ->all();

        $this->merge(['items' => $items]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', 'in:draft,ordered,received'],
            'items' => ['array'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
