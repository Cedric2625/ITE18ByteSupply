<?php

namespace App\Http\Requests\SelectedComponent;

use Illuminate\Foundation\Http\FormRequest;

class StoreSelectedComponentRequest extends FormRequest
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
            'order_id' => ['required', 'exists:orders,id'],
            'component_id' => [
                'required',
                'exists:hardware_components,id',
                'unique:selected_components,component_id,NULL,id,order_id,' . $this->input('order_id'),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
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
            'order_id' => 'order',
            'component_id' => 'component',
            'quantity' => 'quantity',
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
            'component_id.unique' => 'This component has already been added to the order.',
            'quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}