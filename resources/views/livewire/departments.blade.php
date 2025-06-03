<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Departments</h1>
        <div>
            <button wire:click="create" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Create New Department
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="relative size-full p-5">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Departments</h2>
    </div>
    <div class="relative size-full p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($departments as $department)
                <div class="bg-white dark:bg-gray-800 border rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $department->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">{{ $department->description }}</p>
                    <div class="flex justify-end gap-2 items-center">
                        <button wire:click="edit({{ $department->id }})" class="text-blue-600 hover:text-blue-900">
                            <flux:icon.square-pen class="size-5" />
                        </button>
                        <button wire:click="set('confirmDelete', {{$department->id}})" class="text-red-600 hover:text-red-900">
                            <flux:icon.trash-2 class="size-5" />
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <x-modal wire:show="isOpen" title="{{$departmentId ? 'Add New' : 'Edit'}} Department" maxWidth="2xl">
            <form wire:submit.prevent="store">
                <div class="space-y-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            type="text"
                            id="name"
                            wire:model.defer="name"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea
                            id="description"
                            wire:model.defer="description"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            rows="3"
                        ></textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <x-slot name="footer">
                    <button
                        type="button"
                        wire:click="$set('isOpen', false)"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="ml-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                    >
                        {{$departmentId ? 'Update' : 'Add New'}}
                    </button>
                </x-slot>
            </form>
        </x-modal>
        <x-modal wire:show="confirmDelete" title="{{ __('Delete Department') }}" maxWith="md">
            <div class="mb-4">
                <p>{{ __('Are you sure you want to delete this department? This action cannot be undone.') }}</p>
            </div>
            <div class="flex justify-end gap-2">
                <x-flux::button type="button" wire:click="$set('confirmDelete', false)">
                    {{ __('Cancel') }}
                </x-flux::button>
                <x-flux::button type="button" variant="danger" wire:click="delete({{ $confirmDelete }})">
                    {{ __('Delete') }}
                </x-flux::button>
            </div>
        </x-modal>
    </div>
</div>
