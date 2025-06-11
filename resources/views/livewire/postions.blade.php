<div class="container min-h-screen mx-auto px-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Positions</h1>
        <button wire:click="create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Create Position
        </button>
    </div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <svg class="shrink-0 size-5 inline" data-flux-icon="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
              <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif
    <div x-data="{ sortField: @entangle('sortField'), sortDirection: @entangle('sortDirection') }"
        class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <button wire:click="sort('id')" class="flex items-center text-gray-500 hover:text-gray-900">
                            ID
                            <flux:icon.chevron-up-down />
                        </button>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <button wire:click="sort('id')" class="flex items-center text-gray-500 hover:text-gray-900">
                            Name
                            <flux:icon.chevron-up-down />
                        </button>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($positions as $position)
                    <tr>
                        <td class="px-6 py-4 border-b">{{ $position->id }}</td>
                        <td class="px-6 py-4 border-b">{{ $position->name }}</td>
                        <td class="px-6 py-4 border-b text-start">
                            <x-flux::button wire:click="edit({{ $position->id }})" icon="square-pen" secondary>
                                {{ __('Edit') }}
                            </x-flux::button>
                            <x-flux::button wire:click="$set('confirmDelete', {{ $position->id }})" icon="trash" variant="danger">
                                {{ __('Delete') }}
                            </x-flux::button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $positions->links() }}
    </div>

    <x-modal wire:show="showModal" title="{{ $positionID ? 'Edit Position' : 'Create Position' }}">
        <form wire:submit.prevent="savePosition">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Position Name</label>
                <input type="text" id="name" wire:model="position.name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">description</label>
                <textarea wire:model="description" id="description" cols="30" rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <x-slot name="footer">
                <button type="button" wire:click="set('showModal', 0)" class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-700">
                    Cancel
                </button>
                <button wire:click="savePosition" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    {{ $positionID ? 'Update' : 'Add New' }}
                </button>
            </x-slot>
    </x-modal>
    {{-- Confirmation Modal --}}
    <x-modal wire:show="confirmDelete" title="Confirm Delete">
        <p class="mb-4">Are you sure you want to delete this position?</p>
        <x-slot name="footer">
            <button type="button" wire:click="set('confirmDelete', 0)" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                Cancel
            </button>
            <button type="button" wire:click="delete" class="ml-2 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Delete
            </button>
        </x-slot>
    </x-modal>
</div>
