@extends('layouts.app')

@section('title', 'My Orders')

@section('header', 'My Orders')

@section('content')
    <x-table>
        <x-table.head>
            <x-table.heading>Order #</x-table.heading>
            <x-table.heading>Date</x-table.heading>
            <x-table.heading>Total</x-table.heading>
            <x-table.heading>Status</x-table.heading>
            <x-table.heading></x-table.heading>
        </x-table.head>
        <tbody>
        @forelse($orders as $order)
            <x-table.row>
                <x-table.cell>{{ $order->order_reference_number }}</x-table.cell>
                <x-table.cell>{{ $order->order_date->format('M d, Y') }}</x-table.cell>
                <x-table.cell>${{ number_format($order->total_amount, 2) }}</x-table.cell>
                <x-table.cell>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($order->shipping_status === 'completed') bg-green-100 text-green-800
                        @elseif($order->shipping_status === 'shipped' || $order->shipping_status === 'out_for_delivery') bg-blue-100 text-blue-800
                        @elseif($order->shipping_status === 'pending' || $order->shipping_status === 'arrived_hub') bg-yellow-100 text-yellow-800
                        @elseif($order->shipping_status === 'canceled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ str_replace('_',' ', ucfirst($order->shipping_status)) }}
                    </span>
                </x-table.cell>
                <x-table.cell>
                    <a href="{{ route('shop.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                </x-table.cell>
            </x-table.row>
        @empty
            <x-table.row>
                <x-table.cell colspan="5" class="text-center py-8 text-gray-500">No orders yet.</x-table.cell>
            </x-table.row>
        @endforelse
        </tbody>
    </x-table>

    @if($orders->hasPages())
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
@endsection


