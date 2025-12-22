@extends('layouts.app')

@section('title', 'Supplier Details')

@section('header', 'Supplier Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
			<div class="px-4 py-5 sm:px-6 flex justify-between items-center">
				<div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Supplier Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Contact details and supplied components.
                    </p>
				</div>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Supplier name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $supplier->supplier_name }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Contact person
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $supplier->contact_person }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Email address
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <a href="mailto:{{ $supplier->email }}" class="text-blue-600 hover:text-blue-900">
                                {{ $supplier->email }}
                            </a>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Phone number
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $supplier->phone_number }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Total components
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $supplier->hardwareComponents->count() }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Hardware Components Section -->
        @if($supplier->hardwareComponents->isNotEmpty())
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Supplied Components</h4>
                <x-table>
                    <x-table.head>
                        <x-table.heading>Reference #</x-table.heading>
                        <x-table.heading>Name</x-table.heading>
                        <x-table.heading>Category</x-table.heading>
                        <x-table.heading>Price</x-table.heading>
                        <x-table.heading>Actions</x-table.heading>
                    </x-table.head>

					<tbody class="bg-white divide-y divide-gray-200">
						@foreach($supplier->hardwareComponents as $hardwareComponent)
							<x-table.row :even="$loop->even">
								<x-table.cell>{{ $hardwareComponent->component_reference_number }}</x-table.cell>
								<x-table.cell>{{ $hardwareComponent->component_name }}</x-table.cell>
                                <x-table.cell>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
										{{ $hardwareComponent->category->category_name }}
                                    </span>
                                </x-table.cell>
								<x-table.cell>${{ number_format($hardwareComponent->retail_price, 2) }}</x-table.cell>
                                <x-table.cell>
									<a href="{{ route('hardware-components.show', $hardwareComponent) }}" class="text-blue-600 hover:text-blue-900">
                                        View Details
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
