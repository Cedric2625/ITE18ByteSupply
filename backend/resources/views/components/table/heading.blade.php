@props([
    'sortable' => null,
    'direction' => null,
])

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'])->only('class') }}>
    @unless ($sortable)
        <span class="text-left text-xs leading-4 font-medium text-cool-gray-500 uppercase tracking-wider">{{ $slot }}</span>
    @else
        <button {{ $attributes->except('class') }} class="flex items-center space-x-1 text-left text-xs leading-4 font-medium text-cool-gray-500 uppercase tracking-wider group focus:outline-none focus:underline">
            <span>{{ $slot }}</span>

            <span class="relative flex items-center">
                @if ($direction === 'asc')
                    <i class="fas fa-sort-up"></i>
                @elseif ($direction === 'desc')
                    <i class="fas fa-sort-down"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </span>
        </button>
    @endunless
</th>
