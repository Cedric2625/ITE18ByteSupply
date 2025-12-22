@props(['header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white shadow overflow-hidden sm:rounded-lg']) }}>
    @if($header)
        <div class="px-4 py-5 sm:px-6">
            {{ $header }}
        </div>
    @endif

    <div class="border-t border-gray-200">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-4 py-4 sm:px-6 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
