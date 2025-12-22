@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admins.index') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-700">Admins</h3>
            <p class="text-sm text-gray-500 mt-2">Manage system administrators</p>
        </a>

        <a href="{{ route('buyers.index') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-700">Buyers</h3>
            <p class="text-sm text-gray-500 mt-2">Manage buyers and their orders</p>
        </a>

        <a href="{{ route('categories.index') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-700">Categories</h3>
            <p class="text-sm text-gray-500 mt-2">Hardware categories</p>
        </a>

        <a href="{{ route('suppliers.index') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-700">Suppliers</h3>
            <p class="text-sm text-gray-500 mt-2">Manage suppliers</p>
        </a>

        <a href="{{ route('hardware-components.index') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-700">Hardware Components</h3>
            <p class="text-sm text-gray-500 mt-2">Inventory of hardware components</p>
        </a>

        <a href="{{ route('orders.index') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-700">Orders</h3>
            <p class="text-sm text-gray-500 mt-2">Manage and track orders</p>
        </a>
    </div>
@endsection
