@extends('layouts.app')

@section('title', 'Admins')

@section('header', 'Admins')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Admin List</h2>
    </div>

    <x-table>
        <x-table.head>
            <x-table.heading>Username</x-table.heading>
            <x-table.heading>Role</x-table.heading>
            <x-table.heading>Created At</x-table.heading>
            <x-table.heading>Actions</x-table.heading>
        </x-table.head>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($admins as $admin)
                <x-table.row :even="$loop->even">
                    <x-table.cell>{{ $admin->username }}</x-table.cell>
                    <x-table.cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($admin->role === 'system_admin') bg-red-100 text-red-800
                            @elseif($admin->role === 'supply_admin') bg-green-100 text-green-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ $admin->role }}
                        </span>
                    </x-table.cell>
					<x-table.cell>{{ $admin->created_at->format('M d, Y H:i') }}</x-table.cell>
                    <x-table.cell>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admins.show', $admin) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admins.destroy', $admin) }}" method="POST" class="inline"
                                  data-confirm="Are you sure you want to delete this admin?"
                                  data-details="Username: {{ $admin->username }}">
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
                    <x-table.cell colspan="4" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-users text-gray-400 text-5xl"></i>
                            <p class="text-gray-500 text-lg">No admins found.</p>
                            <a href="{{ route('admins.create') }}" class="text-blue-500 hover:text-blue-700">
                                Add your first admin
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </tbody>
    </x-table>
@endsection
