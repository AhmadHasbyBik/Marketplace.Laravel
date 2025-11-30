<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destination_province' => ['required', 'string'],
            'destination_city_name' => ['required', 'string'],
            'destination_city_id' => ['required', 'string'],
            'destination_district_id' => ['nullable', 'string'],
            'destination_district_name' => ['nullable', 'string'],
            'shipping_method_id' => ['nullable', 'exists:shipping_methods,id'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
            'shipping_service' => ['required', 'string'],
            'shipping_courier' => ['required', 'string'],
            'shipping_etd' => ['nullable', 'string'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
