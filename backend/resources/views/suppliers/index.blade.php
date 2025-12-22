@extends('layouts.app')

@section('title', 'Suppliers')

@section('header', 'Suppliers')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Supplier List</h2>
        <a href="{{ route('suppliers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Add New Supplier
        </a>
    </div>

    <x-table>
        <x-table.head>
            <x-table.heading>Supplier Name</x-table.heading>
            <x-table.heading>Contact Person</x-table.heading>
            <x-table.heading>Contact Info</x-table.heading>
            <x-table.heading>Components</x-table.heading>
            <x-table.heading>Actions</x-table.heading>
        </x-table.head>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($suppliers as $supplier)
                <x-table.row :even="$loop->even">
                    <x-table.cell class="font-medium">{{ $supplier->supplier_name }}</x-table.cell>
                    <x-table.cell>{{ $supplier->contact_person }}</x-table.cell>
                    <x-table.cell>
                        <div class="text-sm">
                            <a href="mailto:{{ $supplier->email }}" class="text-blue-600 hover:text-blue-900">
                                {{ $supplier->email }}
                            </a>
                            <br>
                            <span class="text-gray-500">{{ $supplier->phone_number }}</span>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $supplier->hardware_components_count ?? 0 }} components
                        </span>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline"
                                  data-confirm="Are you sure you want to delete this supplier? This will affect all associated components."
                                  data-details="Supplier: {{ $supplier->supplier_name }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
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
                            <i class="fas fa-building text-gray-400 text-5xl"></i>
                            <p class="text-gray-500 text-lg">No suppliers found.</p>
                            <a href="{{ route('suppliers.create') }}" class="text-blue-500 hover:text-blue-700">
                                Add your first supplier
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </tbody>
    </x-table>
@endsection
