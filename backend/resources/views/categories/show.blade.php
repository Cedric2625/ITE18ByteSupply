@extends('layouts.app')

@section('title', 'Category Details')

@section('header', 'Category Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Category Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Details and associated hardware components.
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Category name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $category->category_name }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Total components
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $category->hardwareComponents->count() }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Created at
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $category->created_at->format('F j, Y H:i:s') }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Last updated
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $category->updated_at->format('F j, Y H:i:s') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Hardware Components Section -->
        @if($category->hardwareComponents->isNotEmpty())
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Hardware Components</h4>
                <x-table>
                    <x-table.head>
                        <x-table.heading>Reference #</x-table.heading>
                        <x-table.heading>Name</x-table.heading>
                        <x-table.heading>Brand</x-table.heading>
                        <x-table.heading>Price</x-table.heading>
                        <x-table.heading>Actions</x-table.heading>
                    </x-table.head>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($category->hardwareComponents as $hardwareComponent)
                            <x-table.row :even="$loop->even">
                                <x-table.cell>{{ $hardwareComponent->component_reference_number }}</x-table.cell>
                                <x-table.cell>{{ $hardwareComponent->component_name }}</x-table.cell>
                                <x-table.cell>{{ $hardwareComponent->brand }}</x-table.cell>
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

        <div class="mt-6">
            <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>
@endsection
