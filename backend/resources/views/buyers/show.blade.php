@extends('layouts.app')

@section('title', 'Buyer Details')

@section('header', 'Buyer Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
			<div class="px-4 py-5 sm:px-6 flex justify-between items-center">
				<div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Buyer Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Personal details and order history.
                    </p>
				</div>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Full name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $buyer->buyer_name }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Buyer number
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $buyer->buyer_number }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Email address
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <a href="mailto:{{ $buyer->email }}" class="text-blue-600 hover:text-blue-900">
                                {{ $buyer->email }}
                            </a>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Address
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $buyer->address }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Total orders
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $buyer->orders->count() }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Orders Section -->
        @if($buyer->orders->isNotEmpty())
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Order History</h4>
                <x-table>
                    <x-table.head>
                        <x-table.heading>Order #</x-table.heading>
                        <x-table.heading>Date</x-table.heading>
                        <x-table.heading>Status</x-table.heading>
                        <x-table.heading>Total</x-table.heading>
                        <x-table.heading>Actions</x-table.heading>
                    </x-table.head>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($buyer->orders as $order)
                            <x-table.row :even="$loop->even">
                                <x-table.cell>{{ $order->order_reference_number }}</x-table.cell>
                                <x-table.cell>{{ $order->order_date->format('M d, Y') }}</x-table.cell>
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
                                <x-table.cell>${{ number_format($order->total_amount, 2) }}</x-table.cell>
                                <x-table.cell>
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                        View Details
                                    </a>
                                </x-table.cell>
                            </x-table.row>
                        @endforeach
                    </tbody>
                </x-table>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('buyers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>
@endsection
