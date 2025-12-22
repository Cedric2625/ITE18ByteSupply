@extends('layouts.app')

@section('title', 'Create Admin')

@section('header', 'Create New Admin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('admins.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <x-label for="username" value="Username" />
                <x-input id="username" name="username" type="text" class="mt-1 block w-full" required autofocus
                    value="{{ old('username') }}" />
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="password" value="Password" />
                <x-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="password_confirmation" value="Confirm Password" />
                <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
            </div>

            <div>
                <x-label for="role" value="Role" />
                <select id="role" name="role" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="admin" @if(old('role') === 'admin') selected @endif>Admin</option>
                    <option value="system_admin" @if(old('role') === 'system_admin') selected @endif>System Admin</option>
                    <option value="supply_admin" @if(old('role') === 'supply_admin') selected @endif>Supply Admin</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admins.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>

                <x-button>
                    Create Admin
                </x-button>
            </div>
        </form>
    </div>
@endsection
