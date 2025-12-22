@extends('layouts.app')

@section('title', 'Shop')

@section('header', 'Browse Components')

@section('content')
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-label for="category" value="Category" />
                <select id="category" name="category" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-label for="supplier" value="Supplier" />
                <select id="supplier" name="supplier" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    <option value="">All</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-label for="search" value="Search" />
                <x-input id="search" name="search" type="text" value="{{ request('search') }}" class="mt-1 block w-full" />
            </div>
            <div class="flex items-end">
                <x-button>Filter</x-button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($components as $hardwareComponent)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                <div class="mb-2">
                    <h3 class="text-lg font-semibold">{{ $hardwareComponent->component_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $hardwareComponent->brand }} - {{ $hardwareComponent->model }}</p>
                </div>
                <p class="text-sm text-gray-700 mb-3">{{ $hardwareComponent->specifications }}</p>
                <div class="mt-auto flex items-center justify-between">
                    <div>
                        <div class="text-xl font-bold">${{ number_format($hardwareComponent->retail_price, 2) }}</div>
                        <div class="text-xs text-gray-500">Stock: {{ $hardwareComponent->stock_quantity }}</div>
                    </div>
                    <form action="{{ route('shop.add', $hardwareComponent) }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <x-input name="quantity" type="number" min="1" max="{{ $hardwareComponent->stock_quantity }}" value="1" class="w-20" />
                        <x-button>Add to cart</x-button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No components found.</p>
        @endforelse
    </div>

    @if($components->hasPages())
        <div class="mt-6">{{ $components->links() }}</div>
    @endif
@endsection


