@props(['type' => 'info', 'dismissible' => true])

@php
    $classes = match($type) {
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        default => 'bg-blue-100 border-blue-400 text-blue-700',
    };
@endphp

<div x-data="{ show: true }" x-show="show" {{ $attributes->merge(['class' => "{$classes} border px-4 py-3 rounded relative"]) }} role="alert">
    <span class="block sm:inline">{{ $slot }}</span>
    @if($dismissible)
        <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>
