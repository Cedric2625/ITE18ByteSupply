@extends('layouts.app')

@section('title', 'Buyers')

@section('header', 'Buyers')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h2 class="text-2xl font-semibold">Buyer List</h2>
        <div class="flex-1 md:max-w-md">
            <form action="{{ route('buyers.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search buyers by name, email, or number" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-search"></i></span>
            </form>
        </div>
    </div>

    @isset($stats)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Buyers</div>
            <div class="text-2xl font-semibold">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Active (with orders)</div>
            <div class="text-2xl font-semibold text-green-600">{{ $stats['active'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Inactive</div>
            <div class="text-2xl font-semibold text-gray-700">{{ $stats['inactive'] }}</div>
        </div>
    </div>
    @endisset

    <x-table>
        <x-table.head>
            <x-table.heading>Name</x-table.heading>
            <x-table.heading>Number</x-table.heading>
            <x-table.heading>Email</x-table.heading>
            <x-table.heading>Orders</x-table.heading>
            <x-table.heading>Actions</x-table.heading>
        </x-table.head>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($buyers as $buyer)
                <x-table.row :even="$loop->even">
                    <x-table.cell>{{ $buyer->buyer_name }}</x-table.cell>
                    <x-table.cell>{{ $buyer->buyer_number }}</x-table.cell>
                    <x-table.cell>
                        <a href="mailto:{{ $buyer->email }}" class="text-blue-600 hover:text-blue-900">
                            {{ $buyer->email }}
                        </a>
                    </x-table.cell>
                    <x-table.cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $buyer->orders_count ?? 0 }} orders
                        </span>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('buyers.show', $buyer) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('buyers.destroy', $buyer) }}" method="POST" class="inline"
                                  data-confirm="Are you sure you want to delete this buyer?"
                                  data-details="Buyer: {{ $buyer->buyer_name }} — {{ $buyer->email }}">
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
                    <x-table.cell colspan="5" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-users text-gray-400 text-5xl"></i>
                            <p class="text-gray-500 text-lg">No buyers found.</p>
                            <a href="{{ route('buyers.create') }}" class="text-blue-500 hover:text-blue-700">
                                Add your first buyer
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </tbody>
    </x-table>
@endsection
