@extends('layouts.app')

@section('title', 'Your Cart')

@section('header', 'Your Cart')

@section('content')
    <div class="bg-white rounded-lg shadow p-4">
        @if(empty($cart))
            <p class="text-gray-500">Your cart is empty.</p>
        @else
            <x-table>
                <x-table.head>
                    <x-table.heading>Item</x-table.heading>
                    <x-table.heading>Price</x-table.heading>
                    <x-table.heading>Quantity</x-table.heading>
                    <x-table.heading>Subtotal</x-table.heading>
                    <x-table.heading></x-table.heading>
                </x-table.head>
                <tbody>
                @php($total = 0)
                @foreach($cart as $item)
                    @php($subtotal = $item['price'] * $item['quantity'])
                    @php($total += $subtotal)
                    <x-table.row>
                        <x-table.cell>{{ $item['name'] }}</x-table.cell>
                        <x-table.cell>${{ number_format($item['price'], 2) }}</x-table.cell>
                        <x-table.cell>{{ $item['quantity'] }}</x-table.cell>
                        <x-table.cell>${{ number_format($subtotal, 2) }}</x-table.cell>
                        <x-table.cell>
                            <form action="{{ route('shop.remove', $item['id']) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this item from your cart?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Remove</button>
                            </form>
                        </x-table.cell>
                    </x-table.row>
                @endforeach
                </tbody>
            </x-table>
            <div class="mt-4 flex items-center justify-between">
                <div class="text-xl font-bold">Total: ${{ number_format($total, 2) }}</div>
                <a href="{{ route('shop.checkout') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Checkout</a>
            </div>
        @endif
    </div>
@endsection


