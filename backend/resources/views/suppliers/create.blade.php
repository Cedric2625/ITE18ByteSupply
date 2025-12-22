@extends('layouts.app')

@section('title', 'Create Supplier')

@section('header', 'Create New Supplier')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <x-label for="supplier_name" value="Supplier Name" />
                <x-input id="supplier_name" name="supplier_name" type="text" class="mt-1 block w-full" required autofocus
                    value="{{ old('supplier_name') }}" />
                @error('supplier_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="contact_person" value="Contact Person" />
                <x-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" required
                    value="{{ old('contact_person') }}" />
                @error('contact_person')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="phone_number" value="Phone Number" />
                <x-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full" required
                    value="{{ old('phone_number') }}" placeholder="+1 (555) 123-4567" />
                @error('phone_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="email" value="Email Address" />
                <x-input id="email" name="email" type="email" class="mt-1 block w-full" required
                    value="{{ old('email') }}" />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>

                <x-button>
                    Create Supplier
                </x-button>
            </div>
        </form>
    </div>
@endsection
