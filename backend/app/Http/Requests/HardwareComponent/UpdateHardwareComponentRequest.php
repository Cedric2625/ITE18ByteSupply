<?php

namespace App\Http\Requests\HardwareComponent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHardwareComponentRequest extends FormRequest
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
            'component_reference_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hardware_components')->ignore($this->route('hardware_component')),
            ],
            'component_name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'specifications' => ['required', 'string'],
            'retail_price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'seller_price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'date_created' => ['required', 'date'],
            'date_order' => ['nullable', 'date', 'after_or_equal:date_created'],
            'date_arrive' => ['nullable', 'date', 'after_or_equal:date_order'],
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
            'component_reference_number' => 'reference number',
            'component_name' => 'component name',
            'retail_price' => 'retail price',
            'seller_price' => 'seller price',
            'category_id' => 'category',
            'supplier_id' => 'supplier',
            'date_created' => 'creation date',
            'date_order' => 'order date',
            'date_arrive' => 'arrival date',
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
            'component_reference_number.unique' => 'This reference number has already been taken.',
            'date_order.after_or_equal' => 'The order date must be on or after the creation date.',
            'date_arrive.after_or_equal' => 'The arrival date must be on or after the order date.',
        ];
    }
}