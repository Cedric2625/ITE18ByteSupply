@extends('layouts.app')

@section('title', 'Active Buyers')

@section('header', 'Active Buyers')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold">Buyers with Orders</h2>
        <a href="{{ route('buyers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded">
            <i class="fas fa-list mr-2"></i>All Buyers
        </a>
    </div>

    <x-table>
        <x-table.head>
            <x-table.heading>Name</x-table.heading>
            <x-table.heading>Email</x-table.heading>
            <x-table.heading>Orders</x-table.heading>
            <x-table.heading>Actions</x-table.heading>
        </x-table.head>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($buyers as $buyer)
                <x-table.row :even="$loop->even">
                    <x-table.cell>{{ $buyer->buyer_name }}</x-table.cell>
                    <x-table.cell>
                        <a href="mailto:{{ $buyer->email }}" class="text-blue-600 hover:text-blue-900">{{ $buyer->email }}</a>
                    </x-table.cell>
                    <x-table.cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $buyer->orders_count }} orders
                        </span>
                    </x-table.cell>
                    <x-table.cell>
                        <a href="{{ route('buyers.show', $buyer->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="4" class="text-center py-8 text-gray-500">No active buyers found.</x-table.cell>
                </x-table.row>
            @endforelse
        </tbody>
    </x-table>
@endsection


