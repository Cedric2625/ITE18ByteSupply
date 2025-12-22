@props(['title'])

<div class="px-6 py-4">
    <div class="text-lg font-medium text-gray-900">
        {{ $title }}
    </div>

    <div class="mt-1 text-sm text-gray-600">
        {{ $slot }}
    </div>
</div>
