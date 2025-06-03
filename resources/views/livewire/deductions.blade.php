<div class="container min-h-screen bg-gray-100 p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Deductions</h1>
        <button wire:click="create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add Deduction
        </button>

    </div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    <div>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Type</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deductions as $deduction)
                    <tr>
                        <td class="border px-4 py-2">{{ $deduction->id }}</td>
                        <td class="border px-4 py-2">{{ $deduction->name }}</td>
                        <td class="border px-4 py-2">{{ $deduction->type }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center gap-1">
                                <button wire:click="edit({{ $deduction->id }})" class="text-blue-60 hover:text-blue-900">
                                    <flux:icon.square-pen class="size-5" />
                                </button>
                                <button wire:click="$set('confirmDelete', {{$deduction->id}})" class="text-red-600 hover:text-red-900">
                                    <flux:icon.trash-2 class="size-5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Edit --}}
    <x-modal wire:show="isOpen" title="{{$deductionId ? 'Edit' : 'Add'}} Deductions" maxWith="2xl">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-6">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input wire:model="name" id="name" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                <input wire:model="code" id="code" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('code')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select wire:model="type" id="type"
                    class="mt-1 block w-full border-r-10 outline outline-gray-300 border-transparent rounded-md shadow-sm py-2 px-3 h-[42px] focus:outline-blue-500 focus:ring-blue-500">
                    <option value="">Select Type</option>
                    @foreach ($types as $item)
                        <option value="{{$item}}">{{$item}}</option>
                    @endforeach
                </select>
                @error('type')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="sm:col-span-2 flex flex-col">
                <flux:spacer />
                <label for="apply_all" class="block flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" wire:model="apply_all" id="apply_all" class="rounded border border-gray-300 rounded-md shadow-sm h-4 w-4 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    Applies to all
                </label>
                @error('apply_all')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                <input wire:model="amount" id="amount" type="number" step="0.01"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('amount')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="percentage" class="block text-sm font-medium text-gray-700">Percentage</label>
                <input wire:model="percentage" id="percentage" type="number" step="0.01"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('percentage')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="sm:col-span-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model="description" id="description" cols="30" rows="5"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('description')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <x-slot name="footer">
            <button
                type="button"
                wire:click="$set('isOpen', false)"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50">
                Cancel
            </button>
            <button
                wire:click="store"
                class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                {{$deductionId ? 'Update' : 'Add New'}}
            </button>
        </x-slot>
    </x-modal>
    {{-- Delete Confirmation --}}
    <x-modal wire:show="confirmDelete" title="Delete Deduction" maxWidth="2xl">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <p>Are you sure you want to delete this Deduction?</p>
        </div>
        <x-slot name="footer">
            <button wire:click.prevent="$set('confirmDelete', false)" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
            </button>
            <button wire:click.prevent="delete" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                Delete
            </button>
        </x-slot>
    </x-modal>
</div>
