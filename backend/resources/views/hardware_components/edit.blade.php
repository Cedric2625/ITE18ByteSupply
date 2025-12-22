@extends('layouts.app')

@section('title', 'Edit Hardware Component')

@section('header', 'Edit Hardware Component')

@section('content')
    <div class="max-w-2xl mx-auto">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('hardware-components.update', $hardwareComponent) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <x-label for="component_reference_number" value="Reference Number" />
                <x-input id="component_reference_number" name="component_reference_number" type="text" class="mt-1 block w-full" required
                    value="{{ old('component_reference_number', $hardwareComponent->component_reference_number) }}" />
                @error('component_reference_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="component_name" value="Component Name" />
                <x-input id="component_name" name="component_name" type="text" class="mt-1 block w-full" required
                    value="{{ old('component_name', $hardwareComponent->component_name) }}" />
                @error('component_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="brand" value="Brand" />
                    <x-input id="brand" name="brand" type="text" class="mt-1 block w-full" required
                        value="{{ old('brand', $hardwareComponent->brand) }}" />
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="model" value="Model" />
                    <x-input id="model" name="model" type="text" class="mt-1 block w-full" required
                        value="{{ old('model', $hardwareComponent->model) }}" />
                    @error('model')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <x-label for="specifications" value="Specifications" />
                <textarea id="specifications" name="specifications" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('specifications', $hardwareComponent->specifications) }}</textarea>
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
                            class="pl-7 block w-full" required value="{{ old('retail_price', number_format($hardwareComponent->retail_price, 2, '.', '')) }}" />
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
                            class="pl-7 block w-full" required value="{{ old('seller_price', number_format($hardwareComponent->seller_price, 2, '.', '')) }}" />
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
                            <option value="{{ $category->id }}" {{ old('category_id', $hardwareComponent->category_id) == $category->id ? 'selected' : '' }}>
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
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $hardwareComponent->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                        value="{{ old('date_created', $hardwareComponent->date_created ? $hardwareComponent->date_created->format('Y-m-d') : '') }}" />
                    @error('date_created')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="date_order" value="Order Date" />
                    <x-input id="date_order" name="date_order" type="date" class="mt-1 block w-full"
                        value="{{ old('date_order', $hardwareComponent->date_order ? $hardwareComponent->date_order->format('Y-m-d') : '') }}" />
                    @error('date_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="date_arrive" value="Arrival Date" />
                    <x-input id="date_arrive" name="date_arrive" type="date" class="mt-1 block w-full"
                        value="{{ old('date_arrive', $hardwareComponent->date_arrive ? $hardwareComponent->date_arrive->format('Y-m-d') : '') }}" />
                    @error('date_arrive')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('hardware-components.show', $hardwareComponent) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>

                <x-button>
                    Update Component
                </x-button>
            </div>
        </form>
    </div>
@endsection