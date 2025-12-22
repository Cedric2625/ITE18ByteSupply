@extends('layouts.app')

@section('title', 'Categories')

@section('header', 'Categories')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Category List</h2>
        <a href="{{ route('categories.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Add New Category
        </a>
    </div>

    <x-table>
        <x-table.head>
            <x-table.heading>Category Name</x-table.heading>
            <x-table.heading>Components</x-table.heading>
            <x-table.heading>Created At</x-table.heading>
            <x-table.heading>Actions</x-table.heading>
        </x-table.head>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($categories as $category)
                <x-table.row :even="$loop->even">
                    <x-table.cell class="font-medium">{{ $category->category_name }}</x-table.cell>
                    <x-table.cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $category->hardware_components_count ?? 0 }} components
                        </span>
                    </x-table.cell>
                    <x-table.cell>{{ $category->created_at->format('M d, Y') }}</x-table.cell>
                    <x-table.cell>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('categories.show', $category) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('categories.edit', $category) }}" class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline"
                                  data-confirm="Are you sure you want to delete this category? This will affect all associated components."
                                  data-details="Category: {{ $category->category_name }}">
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
                    <x-table.cell colspan="4" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-folder text-gray-400 text-5xl"></i>
                            <p class="text-gray-500 text-lg">No categories found.</p>
                            <a href="{{ route('categories.create') }}" class="text-blue-500 hover:text-blue-700">
                                Add your first category
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </tbody>
    </x-table>
@endsection
