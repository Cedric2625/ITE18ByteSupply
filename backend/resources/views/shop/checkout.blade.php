@extends('layouts.app')

@section('title', 'Checkout')

@section('header', 'Checkout')

@section('content')
    <div class="bg-white rounded-lg shadow p-4 space-y-6">
        <div>
            <h3 class="text-lg font-semibold mb-2">Order Summary</h3>
            <ul class="space-y-1 text-sm">
                @php($total = 0)
                @foreach($cart as $item)
                    @php($total += $item['price'] * $item['quantity'])
                    <li>{{ $item['name'] }} × {{ $item['quantity'] }} — ${{ number_format($item['price'] * $item['quantity'], 2) }}</li>
                @endforeach
            </ul>
            <div class="mt-2 font-bold">Total: ${{ number_format($total, 2) }}</div>
        </div>

        <form method="POST" action="{{ route('shop.place') }}" class="space-y-4">
            @csrf
            <div>
                <x-label for="payment_method" value="Payment Method" />
                <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md shadow-sm border-gray-300" required>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="online_payment">Online Payment</option>
                </select>
            </div>
            <x-button>Place Order</x-button>
        </form>
    </div>
@endsection


