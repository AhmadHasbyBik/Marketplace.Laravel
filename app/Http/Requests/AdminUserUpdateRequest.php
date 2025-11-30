<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'phone' => ['nullable', 'string', 'max:25'],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
