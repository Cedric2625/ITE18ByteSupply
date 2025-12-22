@extends('layouts.app')

@section('title', 'Create Category')

@section('header', 'Create New Category')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <x-label for="category_name" value="Category Name" />
                <x-input id="category_name" name="category_name" type="text" class="mt-1 block w-full" required autofocus
                    value="{{ old('category_name') }}" />
                @error('category_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>

                <x-button>
                    Create Category
                </x-button>
            </div>
        </form>
    </div>
@endsection
