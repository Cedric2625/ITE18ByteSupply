@extends('layouts.app')

@section('title', 'Edit Buyer')

@section('header', 'Edit Buyer')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('buyers.update', $buyer) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <x-label for="buyer_name" value="Buyer Name" />
                <x-input id="buyer_name" name="buyer_name" type="text" class="mt-1 block w-full" required
                    value="{{ old('buyer_name', $buyer->buyer_name) }}" />
                @error('buyer_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="buyer_number" value="Buyer Number" />
                <x-input id="buyer_number" name="buyer_number" type="text" class="mt-1 block w-full" required
                    value="{{ old('buyer_number', $buyer->buyer_number) }}" />
                @error('buyer_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="email" value="Email Address" />
                <x-input id="email" name="email" type="email" class="mt-1 block w-full" required
                    value="{{ old('email', $buyer->email) }}" />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="address" value="Address" />
                <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('address', $buyer->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('buyers.show', $buyer) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>

                <x-button>
                    Update Buyer
                </x-button>
            </div>
        </form>
    </div>
@endsection
