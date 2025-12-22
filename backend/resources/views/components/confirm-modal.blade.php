@props(['id' => 'confirm-modal', 'title' => 'Confirm Action', 'content' => 'Are you sure you want to perform this action?'])

<x-modal :id="$id">
    <x-modal.header :title="$title">
        {{ $content }}
    </x-modal.header>

    <x-modal.footer>
        <button @click="$dispatch('close-modal', '{{ $id }}')" type="button" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            Cancel
        </button>

        {{ $slot }}
    </x-modal.footer>
</x-modal>
