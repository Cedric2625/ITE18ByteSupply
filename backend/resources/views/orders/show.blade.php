@extends('layouts.app')

@section('title', 'Order Details')

@section('header', 'Order Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Order Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Order details and components.
                    </p>
                </div>
                <!-- Actions removed for view-only; use global Back button -->
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Order reference
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $order->order_reference_number }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Buyer
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div>
                                <a href="{{ route('buyers.show', $order->buyer) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $order->buyer->buyer_name }}
                                </a>
                            </div>
                            <div class="text-gray-500">{{ $order->buyer->buyer_number }}</div>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Admin
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div>{{ $order->admin->username }}</div>
                            <div class="text-gray-500">{{ $order->admin->role }}</div>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Order details
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="space-y-2">
                                <div>
                                    <span class="font-medium">Date:</span> {{ $order->order_date->format('F j, Y') }}
                                </div>
                                <div>
                                    <span class="font-medium">Payment Method:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                </div>
                                <div>
                                    <span class="font-medium">Total Amount:</span> ${{ number_format($order->total_amount, 2) }}
                                </div>
                            </div>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Shipping status
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="space-y-2">
                                <div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($order->shipping_status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->shipping_status === 'shipped' || $order->shipping_status === 'out_for_delivery') bg-blue-100 text-blue-800
                                        @elseif($order->shipping_status === 'pending' || $order->shipping_status === 'arrived_hub') bg-yellow-100 text-yellow-800
                                        @elseif($order->shipping_status === 'canceled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', ucfirst($order->shipping_status)) }}
                                    </span>
                                </div>
                                @if($order->tracking_number)
                                    <div>
                                        <span class="font-medium">Tracking Number:</span> {{ $order->tracking_number }}
                                    </div>
                                @endif
                                @if($order->estimated_delivery)
                                    <div>
                                        <span class="font-medium">Estimated Delivery:</span> {{ $order->estimated_delivery->format('F j, Y') }}
                                    </div>
                                @endif
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Components Section -->
        <div class="mt-8">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Order Components</h4>
            <x-table>
                <x-table.head>
                    <x-table.heading>Component</x-table.heading>
                    <x-table.heading>Category</x-table.heading>
                    <x-table.heading>Supplier</x-table.heading>
                    <x-table.heading>Quantity</x-table.heading>
                    <x-table.heading>Unit Price</x-table.heading>
                    <x-table.heading>Total</x-table.heading>
                </x-table.head>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->selectedComponents as $selected)
                        <x-table.row :even="$loop->even">
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
                            <x-table.cell>{{ $selected->hardwareComponent->category->category_name }}</x-table.cell>
                            <x-table.cell>{{ $selected->hardwareComponent->supplier->supplier_name }}</x-table.cell>
                            <x-table.cell>{{ $selected->quantity }}</x-table.cell>
                            <x-table.cell>${{ number_format($selected->hardwareComponent->retail_price, 2) }}</x-table.cell>
                            <x-table.cell>${{ number_format($selected->hardwareComponent->retail_price * $selected->quantity, 2) }}</x-table.cell>
                        </x-table.row>
                    @endforeach
                    <x-table.row>
                        <x-table.cell colspan="5" class="text-right font-medium">Total Amount:</x-table.cell>
                        <x-table.cell class="font-bold">${{ number_format($order->total_amount, 2) }}</x-table.cell>
                    </x-table.row>
                </tbody>
            </x-table>
        </div>

        <!-- Back to List removed; use global Back button in header -->
    </div>
@endsection
