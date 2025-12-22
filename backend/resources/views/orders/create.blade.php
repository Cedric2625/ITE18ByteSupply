@extends('layouts.app')

@section('title', 'Create Order')

@section('header', 'Create New Order')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('orders.store') }}" method="POST" class="space-y-6" x-data="orderForm()">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="buyer_id" value="Buyer" />
                    <select id="buyer_id" name="buyer_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="">Select Buyer</option>
                        @foreach($buyers as $buyer)
                            <option value="{{ $buyer->id }}" {{ old('buyer_id') == $buyer->id ? 'selected' : '' }}>
                                {{ $buyer->buyer_name }} ({{ $buyer->buyer_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('buyer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="admin_id" value="Admin" />
                    <select id="admin_id" name="admin_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="">Select Admin</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ old('admin_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->username }} ({{ $admin->role }})
                            </option>
                        @endforeach
                    </select>
                    @error('admin_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="order_date" value="Order Date" />
                    <x-input id="order_date" name="order_date" type="date" class="mt-1 block w-full" required
                        value="{{ old('order_date', date('Y-m-d')) }}" />
                    @error('order_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="payment_method" value="Payment Method" />
                    <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="">Select Payment Method</option>
                        <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        <option value="debit_card" {{ old('payment_method') === 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Components Section -->
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Order Components</h3>
                    <button type="button" @click="addComponent()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-plus mr-2"></i>Add Component
                    </button>
                </div>

                <template x-for="(component, index) in components" :key="index">
                    <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-sm font-medium text-gray-700">Component #<span x-text="index + 1"></span></h4>
                            <button type="button" @click="removeComponent(index)" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label x-bind:for="'selected_components[' + index + '][component_id]'" value="Hardware Component" />
                                <select x-bind:name="'selected_components[' + index + '][component_id]'" x-bind:id="'selected_components[' + index + '][component_id]'" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Select Component</option>
                                    @foreach($components as $hardwareComponent)
                                        <option value="{{ $hardwareComponent->id }}">
                                            {{ $hardwareComponent->component_name }} ({{ $hardwareComponent->component_reference_number }}) - ${{ number_format($hardwareComponent->retail_price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label x-bind:for="'selected_components[' + index + '][quantity]'" value="Quantity" />
                                <x-input x-bind:name="'selected_components[' + index + '][quantity]'" x-bind:id="'selected_components[' + index + '][quantity]'" type="number" min="1" class="mt-1 block w-full" required />
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="components.length === 0" class="text-center py-4 text-gray-500">
                    No components added yet. Click "Add Component" to start building your order.
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>

                <x-button>
                    Create Order
                </x-button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function orderForm() {
            return {
                components: [],
                addComponent() {
                    this.components.push({});
                },
                removeComponent(index) {
                    this.components.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
@endsection