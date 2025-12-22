@extends('layouts.app')

@section('title', 'Selected Components')

@section('header', 'Selected Components')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Selected Components List</h2>
        <a href="{{ route('orders.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Create New Order
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('selected-components.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-label for="order" value="Order" />
                <select id="order" name="order" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Orders</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" {{ request('order') == $order->id ? 'selected' : '' }}>
                            {{ $order->order_reference_number }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-label for="component" value="Component" />
                <select id="component" name="component" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Components</option>
                    @foreach($components as $hardwareComponent)
                        <option value="{{ $hardwareComponent->id }}" {{ request('component') == $hardwareComponent->id ? 'selected' : '' }}>
                            {{ $hardwareComponent->component_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-label for="quantity" value="Minimum Quantity" />
                <x-input id="quantity" type="number" name="quantity" class="mt-1 block w-full" min="1"
                    value="{{ request('quantity') }}" placeholder="Enter minimum quantity" />
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('selected-components.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <x-table>
        <x-table.head>
            <x-table.heading>Order #</x-table.heading>
            <x-table.heading>Component</x-table.heading>
            <x-table.heading>Quantity</x-table.heading>
            <x-table.heading>Unit Price</x-table.heading>
            <x-table.heading>Total</x-table.heading>
            <x-table.heading>Actions</x-table.heading>
        </x-table.head>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($selectedComponents as $selected)
                <x-table.row :even="$loop->even">
                    <x-table.cell>
                        <a href="{{ route('orders.show', $selected->order) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $selected->order->order_reference_number }}
                        </a>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">
                                <a href="{{ route('hardware-components.show', $selected->hardwareComponent) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $selected->hardwareComponent->component_name }}
                                </a>
                            </div>
                            <div class="text-gray-500">{{ $selected->hardwareComponent->component_reference_number }}</div>
                        </div>
                    </x-table.cell>
                    <x-table.cell>{{ $selected->quantity }}</x-table.cell>
                    <x-table.cell>${{ number_format($selected->hardwareComponent->retail_price, 2) }}</x-table.cell>
                    <x-table.cell>${{ number_format($selected->hardwareComponent->retail_price * $selected->quantity, 2) }}</x-table.cell>
                    <x-table.cell>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('orders.show', $selected->order) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('orders.edit', $selected->order) }}" class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="6" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-box text-gray-400 text-5xl"></i>
                            <p class="text-gray-500 text-lg">No selected components found.</p>
                            <a href="{{ route('orders.create') }}" class="text-blue-500 hover:text-blue-700">
                                Create a new order
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </tbody>
    </x-table>

    <!-- Pagination -->
    @if($selectedComponents->hasPages())
        <div class="mt-4">
            {{ $selectedComponents->links() }}
        </div>
    @endif
@endsection
