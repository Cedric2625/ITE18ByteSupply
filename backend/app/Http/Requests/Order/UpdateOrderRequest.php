<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'order_date' => ['sometimes','required', 'date'],
            'total_amount' => ['sometimes','required', 'numeric', 'min:0', 'decimal:0,2'],
            'shipping_status' => ['sometimes','required', 'string', 'in:pending,processing,shipped,arrived_hub,out_for_delivery,completed,canceled'],
            'tracking_number' => ['sometimes','nullable', 'string', 'max:255'],
            'estimated_delivery' => ['sometimes','nullable', 'date', 'after:order_date'],
            'order_reference_number' => [
                'sometimes','required',
                'string',
                'max:255',
                Rule::unique('orders')->ignore($this->route('order')),
            ],
            'buyer_id' => ['sometimes','required', 'exists:buyers,id'],
            'admin_id' => ['sometimes','required', 'exists:admins,id'],
            'payment_method' => ['sometimes','required', 'string', 'in:cash,credit_card,debit_card,bank_transfer,online_payment'],
            'selected_components' => ['sometimes','array','min:1'],
            'selected_components.*.component_id' => ['sometimes','required', 'exists:hardware_components,id'],
            'selected_components.*.quantity' => ['sometimes','required', 'integer', 'min:1'],
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
            'order_date' => 'order date',
            'total_amount' => 'total amount',
            'shipping_status' => 'shipping status',
            'tracking_number' => 'tracking number',
            'estimated_delivery' => 'estimated delivery date',
            'order_reference_number' => 'order reference number',
            'buyer_id' => 'buyer',
            'admin_id' => 'admin',
            'payment_method' => 'payment method',
            'selected_components' => 'selected components',
            'selected_components.*.component_id' => 'component',
            'selected_components.*.quantity' => 'quantity',
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
            'order_reference_number.unique' => 'This order reference number has already been taken.',
            'estimated_delivery.after' => 'The estimated delivery date must be after the order date.',
            'selected_components.min' => 'At least one component must be selected.',
            'selected_components.*.quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}