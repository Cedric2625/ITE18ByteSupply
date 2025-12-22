@extends('layouts.app')

@section('title', 'Hardware Component Details')

@section('header', 'Hardware Component Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Component Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Details and specifications about the component.
                    </p>
                </div>
                <!-- Actions removed for view-only; use global Back button -->
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Reference number
                        </dt>
						<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							{{ $hardwareComponent->component_reference_number }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Component name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							{{ $hardwareComponent->component_name }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Brand & Model
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							{{ $hardwareComponent->brand }} - {{ $hardwareComponent->model }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Category
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							<a href="{{ route('categories.show', $hardwareComponent->category) }}" class="text-blue-600 hover:text-blue-900">
								{{ $hardwareComponent->category->category_name }}
                            </a>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Supplier
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							<a href="{{ route('suppliers.show', $hardwareComponent->supplier) }}" class="text-blue-600 hover:text-blue-900">
								{{ $hardwareComponent->supplier->supplier_name }}
                            </a>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Specifications
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							{{ $hardwareComponent->specifications }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Pricing
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="space-y-1">
                                <div>
									<span class="font-medium">Retail Price:</span> ${{ number_format($hardwareComponent->retail_price, 2) }}
                                </div>
                                <div>
									<span class="font-medium">Seller Price:</span> ${{ number_format($hardwareComponent->seller_price, 2) }}
                                </div>
                            </div>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Dates
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="space-y-1">
                                <div>
									<span class="font-medium">Created:</span> {{ $hardwareComponent->date_created?->format('F j, Y') }}
                                </div>
								@if($hardwareComponent->date_order)
                                    <div>
										<span class="font-medium">Ordered:</span> {{ $hardwareComponent->date_order->format('F j, Y') }}
                                    </div>
                                @endif
								@if($hardwareComponent->date_arrive)
                                    <div>
										<span class="font-medium">Arrived:</span> {{ $hardwareComponent->date_arrive->format('F j, Y') }}
                                    </div>
                                @endif
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Orders Section -->
		@if($hardwareComponent->selectedComponents->isNotEmpty())
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Order History</h4>
                <x-table>
                    <x-table.head>
                        <x-table.heading>Order #</x-table.heading>
                        <x-table.heading>Buyer</x-table.heading>
                        <x-table.heading>Quantity</x-table.heading>
                        <x-table.heading>Status</x-table.heading>
                        <x-table.heading>Actions</x-table.heading>
                    </x-table.head>

					<tbody class="bg-white divide-y divide-gray-200">
						@foreach($hardwareComponent->selectedComponents as $selected)
                            <x-table.row :even="$loop->even">
                                <x-table.cell>{{ $selected->order->order_reference_number }}</x-table.cell>
                                <x-table.cell>{{ $selected->order->buyer->buyer_name }}</x-table.cell>
                                <x-table.cell>{{ $selected->quantity }}</x-table.cell>
                                <x-table.cell>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($selected->order->shipping_status === 'delivered') bg-green-100 text-green-800
                                        @elseif($selected->order->shipping_status === 'shipped') bg-blue-100 text-blue-800
                                        @elseif($selected->order->shipping_status === 'processing') bg-yellow-100 text-yellow-800
                                        @elseif($selected->order->shipping_status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($selected->order->shipping_status) }}
                                    </span>
                                </x-table.cell>
                                <x-table.cell>
                                    <a href="{{ route('orders.show', $selected->order) }}" class="text-blue-600 hover:text-blue-900">
                                        View Order
                                    </a>
                                </x-table.cell>
                            </x-table.row>
                        @endforeach
                    </tbody>
                </x-table>
            </div>
        @endif

        <!-- Back to List removed; use global Back button in header -->
    </div>
@endsection
