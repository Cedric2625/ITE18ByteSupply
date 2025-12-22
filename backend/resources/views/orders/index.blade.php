@extends('layouts.app')

@section('title', 'Orders')

@section('header', 'Orders')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Order List</h2>
        <a href="{{ route('orders.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Create New Order
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-label for="buyer" value="Buyer" />
                <select id="buyer" name="buyer" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Buyers</option>
                    @foreach($buyers as $buyer)
                        <option value="{{ $buyer->id }}" {{ request('buyer') == $buyer->id ? 'selected' : '' }}>
                            {{ $buyer->buyer_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-label for="status" value="Status" />
                <select id="status" name="status" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <x-label for="date_range" value="Date Range" />
                <div class="grid grid-cols-2 gap-2">
                    <x-input type="date" name="start_date" class="mt-1 block w-full"
                        value="{{ request('start_date') }}" placeholder="From" />
                    <x-input type="date" name="end_date" class="mt-1 block w-full"
                        value="{{ request('end_date') }}" placeholder="To" />
                </div>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <x-table>
        <x-table.head>
            <x-table.heading>Order #</x-table.heading>
            <x-table.heading>Buyer</x-table.heading>
            <x-table.heading>Date</x-table.heading>
            <x-table.heading>Total</x-table.heading>
            <x-table.heading>Status</x-table.heading>
            <x-table.heading>Actions</x-table.heading>
        </x-table.head>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($orders as $order)
                <x-table.row :even="$loop->even">
                    <x-table.cell>{{ $order->order_reference_number }}</x-table.cell>
                    <x-table.cell>
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $order->buyer->buyer_name }}</div>
                            <div class="text-gray-500">{{ $order->buyer->buyer_number }}</div>
                        </div>
                    </x-table.cell>
                    <x-table.cell>{{ $order->order_date->format('M d, Y') }}</x-table.cell>
                    <x-table.cell>${{ number_format($order->total_amount, 2) }}</x-table.cell>
                    <x-table.cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($order->shipping_status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->shipping_status === 'shipped') bg-blue-100 text-blue-800
                            @elseif($order->shipping_status === 'processing') bg-yellow-100 text-yellow-800
                            @elseif($order->shipping_status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($order->shipping_status) }}
                        </span>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('orders.edit', $order) }}" class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline"
                                  data-confirm="Are you sure you want to delete this order?"
                                  data-details="Order: {{ $order->order_reference_number }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="6" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-shopping-cart text-gray-400 text-5xl"></i>
                            <p class="text-gray-500 text-lg">No orders found.</p>
                            <a href="{{ route('orders.create') }}" class="text-blue-500 hover:text-blue-700">
                                Create your first order
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </tbody>
    </x-table>

    <!-- Status Legend -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="flex items-center space-x-2"><span class="inline-block w-3 h-3 rounded-full bg-yellow-200"></span><span class="text-sm text-gray-600">Pending / Arrived Hub</span></div>
        <div class="flex items-center space-x-2"><span class="inline-block w-3 h-3 rounded-full bg-blue-200"></span><span class="text-sm text-gray-600">Processing / Shipped / Out for delivery</span></div>
        <div class="flex items-center space-x-2"><span class="inline-block w-3 h-3 rounded-full bg-green-200"></span><span class="text-sm text-gray-600">Completed / Delivered</span></div>
        <div class="flex items-center space-x-2"><span class="inline-block w-3 h-3 rounded-full bg-red-200"></span><span class="text-sm text-gray-600">Cancelled</span></div>
        <div class="flex items-center space-x-2"><span class="inline-block w-3 h-3 rounded-full bg-gray-200"></span><span class="text-sm text-gray-600">Other</span></div>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
