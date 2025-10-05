<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_picture' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,heic,heif', 'max:10240'], // 10MB max - compatible with most servers
            'remove_profile_picture' => ['nullable', 'boolean'],
        ];

        // Add address validation for tenants
        if ($this->user()->role === 'tenant') {
            $rules['address'] = ['nullable', 'string', 'max:500'];
            $rules['city'] = ['nullable', 'string', 'max:100'];
            $rules['province'] = ['nullable', 'string', 'max:100'];
        }

        return $rules;
    }
}
