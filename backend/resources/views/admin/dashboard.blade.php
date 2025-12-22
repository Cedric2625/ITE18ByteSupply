@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('header', 'Admin Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Orders</div>
            <div class="text-2xl font-semibold">{{ $stats['orders'] }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Buyers</div>
            <div class="text-2xl font-semibold">{{ $stats['buyers'] }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Components</div>
            <div class="text-2xl font-semibold">{{ $stats['components'] }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Suppliers</div>
            <div class="text-2xl font-semibold">{{ $stats['suppliers'] }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Categories</div>
            <div class="text-2xl font-semibold">{{ $stats['categories'] }}</div>
        </div>
    </div>

    <div class="bg-white rounded shadow">
        <div class="p-4 border-b font-semibold">Recent Orders</div>
        <div class="p-4">
            <x-table>
                <x-table.head>
                    <x-table.heading>Ref #</x-table.heading>
                    <x-table.heading>Buyer</x-table.heading>
                    <x-table.heading>Status</x-table.heading>
                    <x-table.heading>Total</x-table.heading>
                    <x-table.heading>Placed</x-table.heading>
                    <x-table.heading></x-table.heading>
                </x-table.head>
                <tbody>
                    @foreach($recentOrders as $order)
                        <x-table.row>
                            <x-table.cell>{{ $order->order_reference_number }}</x-table.cell>
                            <x-table.cell>{{ $order->buyer->buyer_name }}</x-table.cell>
                            <x-table.cell>{{ ucfirst($order->shipping_status) }}</x-table.cell>
                            <x-table.cell>${{ number_format($order->total_amount, 2) }}</x-table.cell>
                            <x-table.cell>{{ $order->created_at->diffForHumans() }}</x-table.cell>
                            <x-table.cell>
                                <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                            </x-table.cell>
                        </x-table.row>
                    @endforeach
                </tbody>
            </x-table>
        </div>
    </div>
@endsection


