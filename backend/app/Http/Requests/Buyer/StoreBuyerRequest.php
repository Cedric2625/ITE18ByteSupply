<?php

namespace App\Http\Requests\Buyer;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuyerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add proper authorization logic later
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_number' => ['required', 'string', 'max:255', 'unique:buyers'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:buyers'],
            'address' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'buyer_name' => 'name',
            'buyer_number' => 'buyer number',
            'email' => 'email address',
            'address' => 'address',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'buyer_number.unique' => 'This buyer number has already been taken.',
            'email.unique' => 'This email address has already been registered.',
        ];
    }
}