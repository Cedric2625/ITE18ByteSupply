@props(['type' => 'default'])

@php
    $classes = match($type) {
        'success' => 'bg-green-100 text-green-800',
        'error' => 'bg-red-100 text-red-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'info' => 'bg-blue-100 text-blue-800',
        'pending' => 'bg-gray-100 text-gray-800',
        'processing' => 'bg-yellow-100 text-yellow-800',
        'shipped' => 'bg-blue-100 text-blue-800',
        'delivered' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp

<span {{ $attributes->merge(['class' => "px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$classes}"]) }}>
    {{ $slot }}
</span>
