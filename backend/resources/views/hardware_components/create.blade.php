@extends('layouts.app')

@section('title', 'Create Hardware Component')

@section('header', 'Create New Hardware Component')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('hardware-components.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <x-label for="component_reference_number" value="Reference Number" />
                <x-input id="component_reference_number" name="component_reference_number" type="text" class="mt-1 block w-full" required autofocus
                    value="{{ old('component_reference_number') }}" />
                @error('component_reference_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="component_name" value="Component Name" />
                <x-input id="component_name" name="component_name" type="text" class="mt-1 block w-full" required
                    value="{{ old('component_name') }}" />
                @error('component_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="brand" value="Brand" />
                    <x-input id="brand" name="brand" type="text" class="mt-1 block w-full" required
                        value="{{ old('brand') }}" />
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="model" value="Model" />
                    <x-input id="model" name="model" type="text" class="mt-1 block w-full" required
                        value="{{ old('model') }}" />
                    @error('model')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <x-label for="specifications" value="Specifications" />
                <textarea id="specifications" name="specifications" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('specifications') }}</textarea>
                @error('specifications')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="retail_price" value="Retail Price" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <x-input id="retail_price" name="retail_price" type="number" step="0.01" min="0"
                            class="pl-7 block w-full" required value="{{ old('retail_price') }}" />
                    </div>
                    @error('retail_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="seller_price" value="Seller Price" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <x-input id="seller_price" name="seller_price" type="number" step="0.01" min="0"
                            class="pl-7 block w-full" required value="{{ old('seller_price') }}" />
                    </div>
                    @error('seller_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="category_id" value="Category" />
                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="supplier_id" value="Supplier" />
                    <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-label for="date_created" value="Creation Date" />
                    <x-input id="date_created" name="date_created" type="date" class="mt-1 block w-full" required
                        value="{{ old('date_created', date('Y-m-d')) }}" />
                    @error('date_created')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="date_order" value="Order Date" />
                    <x-input id="date_order" name="date_order" type="date" class="mt-1 block w-full"
                        value="{{ old('date_order') }}" />
                    @error('date_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="date_arrive" value="Arrival Date" />
                    <x-input id="date_arrive" name="date_arrive" type="date" class="mt-1 block w-full"
                        value="{{ old('date_arrive') }}" />
                    @error('date_arrive')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('hardware-components.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>

                <x-button>
                    Create Component
                </x-button>
            </div>
        </form>
    </div>
@endsection
