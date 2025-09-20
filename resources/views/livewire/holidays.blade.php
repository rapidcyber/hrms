<div class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">{{ __('Holidays') }}</h1>
            <div class="flex items-center gap-4">
                {{-- Search input --}}
                <x-flux::input
                    wire:model.live="search"
                    type="search"
                    placeholder="{{ __('Search holidays...') }}"
                    class="bg-white border rounded-md"
                />
                {{-- <input wire:model.live="search" type="search" placeholder="Search employees..." class="px-4 bg-white py-2 border rounded-md"> --}}
                <x-flux::button wire:click="create" icon="plus" primary>
                    {{ __('Add Holiday') }}
                </x-flux::button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <x-flux.table
                :striped="true"
                :hover="true"
                :bordered="false"
                responsive
                id="holidays-table"
                class="mb-6"
                wire:loading.class="opacity-50"
                wire:target="sort, delete, create"
            >
                <x-slot:head>
                    <x-flux.table.heading wire:click="sort('name')" sortable sort-by="name" direction="{{ $sortDirection['name'] }}" width="40%">
                        {{ __('Holiday Name') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading wire:click="sort('date')" sortable sort-by="date" direction="{{ $sortDirection['date'] }}" width="30%">
                        {{ __('Date') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading width="30%">{{ __('Actions') }}</x-flux.table.heading>
                </x-slot:head>

                <x-slot:body>
                    @foreach($holidays as $holiday)
                        <x-flux.table.row :even="$loop->even">
                            <x-flux.table.cell>{{ $holiday->name }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ \Carbon\Carbon::parse($holiday->date)->format('F j, Y') }}</x-flux.table.cell>
                            <x-flux.table.cell class="text-right">
                                <x-flux::button wire:click="edit({{ $holiday->id }})" icon="pencil">
                                    {{ __('Edit') }}
                                </x-flux::button>
                                <x-flux::button wire:click="$set('confirmDelete',{{ $holiday->id }})" icon="trash" variant="danger">
                                    {{ __('Delete') }}
                                </x-flux::button>
                            </x-flux.table.cell>
                        </x-flux.table.row>
                    @endforeach
                    @if($holidays->isEmpty())
                        <x-flux.table.row>
                            <x-flux.table.cell colspan="3" class="text-center text-gray-500">
                                {{ __('No holidays found.') }}
                            </x-flux.table.cell>
                        </x-flux.table.row>
                    @endif
                </x-slot:body>
            </x-flux.table>
        </div>
        @if($holidays->hasPages())
            <div class="mt-4">
                {{ $holidays->links() }}
            </div>
        @endif
    </div>
    <x-modal wire:show="isOpen" title="{{ $holidayId ? __('Edit Holiday') : __('Add Holiday') }}" maxWith="2xl">
        <form wire:submit.prevent="store">
            <div class="mb-4">
                <x-flux::input
                    wire:model.defer="name"
                    label="{{ __('Holiday Name') }}"
                    placeholder="{{ __('Enter holiday name') }}"
                    required
                />
                @error('name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4 flex gap-4">
                <div class="w-full">
                    <x-flux::input
                        wire:model.defer="date"
                        type="date"
                        label="{{ __('Date') }}"
                        required
                    />
                @error('date')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
                </div>
                <div class="w-full">
                    <x-flux::select
                        wire:model.defer="type"
                        placeholder="{{ __('Select type') }}"
                        label="{{ __('Type') }}"
                        required>
                        @foreach ($types as $type)
                            <x-flux::select.option :value="$type">{{ $type }}</x-flux::select.option>
                        @endforeach

                    </x-flux::select>
                </div>

            </div>

            <div class="mb-4">
                <x-flux::textarea
                    wire:model.defer="description"
                    label="{{ __('Description') }}"
                    placeholder='Details of the holiday'
                />
            </div>
            <div class="flex justify-end gap-2">
                <x-flux::button type="button" wire:click="$set('isOpen', false)">
                    {{ __('Cancel') }}
                </x-flux::button>
                <x-flux::button type="submit" variant="primary">
                    {{ $holidayId ? __('Update') : __('Save') }}
                </x-flux::button>
            </div>
        </form>
    </x-modal>
    <x-modal wire:show="confirmDelete" title="{{ __('Delete Holiday') }}" maxWith="md">
        <div class="mb-4">
            <p>{{ __('Are you sure you want to delete this holiday? This action cannot be undone.') }}</p>
        </div>
        <div class="flex justify-end gap-2">
            <x-flux::button type="button" wire:click="$set('confirmDelete', null)">
                {{ __('Cancel') }}
            </x-flux::button>
            <x-flux::button type="button" variant="danger" wire:click="delete({{ $confirmDelete }})">
                {{ __('Delete') }}
            </x-flux::button>
        </div>
    </x-modal>
</div>
