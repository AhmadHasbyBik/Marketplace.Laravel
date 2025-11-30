<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'media_id' => ['required', 'exists:media,id'],
            'cta_text' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'url'],
            'type' => ['required', 'in:hero,promo,secondary'],
            'is_active' => ['boolean'],
        ];
    }
}
