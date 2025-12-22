@extends('layouts.app')

@section('title', 'Hardware Components')

@section('header', 'Hardware Components')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Hardware Component List</h2>
        <a href="{{ route('hardware-components.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Add New Component
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('hardware-components.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-label for="category" value="Category" />
                <select id="category" name="category" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-label for="supplier" value="Supplier" />
                <select id="supplier" name="supplier" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-label for="search" value="Search" />
                <x-input id="search" type="text" name="search" class="mt-1 block w-full" value="{{ request('search') }}"
                    placeholder="Search by name, brand, or model..." />
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('hardware-components.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <x-table>
        <x-table.head>
            <x-table.heading>Reference #</x-table.heading>
            <x-table.heading>Name</x-table.heading>
            <x-table.heading>Category</x-table.heading>
            <x-table.heading>Supplier</x-table.heading>
            <x-table.heading>Price</x-table.heading>
            <x-table.heading>Status</x-table.heading>
            <x-table.heading>Actions</x-table.heading>
        </x-table.head>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($components as $hardwareComponent)
                <x-table.row :even="$loop->even">
                    <x-table.cell>{{ $hardwareComponent->component_reference_number }}</x-table.cell>
                    <x-table.cell>
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $hardwareComponent->component_name }}</div>
                            <div class="text-gray-500">{{ $hardwareComponent->brand }} - {{ $hardwareComponent->model }}</div>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ $hardwareComponent->category->category_name }}
                        </span>
                    </x-table.cell>
                    <x-table.cell>{{ $hardwareComponent->supplier->supplier_name }}</x-table.cell>
                    <x-table.cell>
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">${{ number_format($hardwareComponent->retail_price, 2) }}</div>
                            <div class="text-gray-500">${{ number_format($hardwareComponent->seller_price, 2) }} (seller)</div>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        @if($hardwareComponent->selectedComponents->count() > 0)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                In Orders
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Available
                            </span>
                        @endif
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('hardware-components.show', $hardwareComponent) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('hardware-components.edit', $hardwareComponent) }}" class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('hardware-components.destroy', $hardwareComponent) }}" method="POST" class="inline"
                                  data-confirm="Are you sure you want to delete this component?"
                                  data-details="Component: {{ $hardwareComponent->component_name }} ({{ $hardwareComponent->component_reference_number }})">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="7" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-microchip text-gray-400 text-5xl"></i>
                            <p class="text-gray-500 text-lg">No hardware components found.</p>
                            <a href="{{ route('hardware-components.create') }}" class="text-blue-500 hover:text-blue-700">
                                Add your first component
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </tbody>
    </x-table>

    <!-- Pagination -->
    @if($components->hasPages())
        <div class="mt-4">
            {{ $components->links() }}
        </div>
    @endif
@endsection
