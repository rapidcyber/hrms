<div class="min-h-screen">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ __('Leave Management') }}</h1>
        <div class="flex items-center gap-4">
            {{-- Search input --}}
            <x-flux::input
                wire:model.live="search"
                type="search"
                placeholder="{{ __('Search leaves...') }}"
                class="bg-white border rounded-md"
            />
            <x-flux::button wire:click="create" icon="plus" primary>
                {{ __('Add Leave') }}
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
            id="leaves-table"
            class="mb-6"
            wire:loading.class="opacity-50"
            wire:target="sortBy, deleteLeave, createLeave"
        >
            <x-slot:head>
                <x-flux.table.heading wire:click="sort('employee_name')" sortable sort-by="employee_name" direction="{{ $sortDirection['employee_name'] }}" width="30%">
                    {{ __('Employee Name') }}
                </x-flux.table.heading>
                <x-flux.table.heading wire:click="sort('start_date')" sortable sort-by="start_date" direction="{{ $sortDirection['start_date'] }}" width="20%">
                    {{ __('Start Date') }}
                </x-flux.table.heading>
                <x-flux.table.heading wire:click="sort('status')" sortable sort-by="status" direction="{{ $sortDirection['status'] }}" width="20%">
                    {{ __('Status') }}
                </x-flux.table.heading>
                <x-flux.table.heading width="10%">{{ __('Actions') }}</x-flux.table.heading>
            </x-slot:head>

            <x-slot:body>
                @foreach($leaves as $leave)
                    <x-flux.table.row :even="$loop->even">
                        <x-flux.table.cell>{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</x-flux.table.cell>
                        <x-flux.table.cell>{{ \Carbon\Carbon::parse($leave->start_date)->format('F j, Y') }}</x-flux.table.cell>
                        <x-flux.table.cell>{{ $leave->status }}</x-flux.table.cell>
                        <x-flux.table.cell class="text-right">
                            <x-flux::button wire:click="edit({{ $leave->id }})" icon="pencil" secondary>
                                {{ __('Edit') }}
                            </x-flux::button>
                            <x-flux::button wire:click="$sent('confirmDelete',{{ $leave->id }})" icon="trash" danger>
                                {{ __('Delete') }}
                            </x-flux::button>
                        </x-flux.table.cell>
                    </x-flux.table.row>
                @endforeach
                @if($leaves->isEmpty())
                    <x-flux.table.row>
                        <x-flux.table.cell colspan="5" class="text-center text-gray-500">
                            {{ __('No leaves found.') }}
                        </x-flux.table.cell>
                    </x-flux.table.row>
                @endif
            </x-slot:body>
        </x-flux.table>
        @if($leaves->hasPages())
            <div class="mt-4">
                {{ $leaves->links() }}
            </div>
        @endif

    </div>
    <x-modal wire:show="isOpen" maxWith="2xl" title="{{$selectedLeaveId ? __('Edit Leave') : __('Add Leave')}}">
        <form wire:submit.prevent="store">
            @csrf
            <div class="space-y-4">

                <div class="flex gap-4">
                    <x-flux::select
                        wire:model.defer="employee_id"
                        placeholder="{{ __('Select Employee') }}"
                        label="{{ __('Employee') }}"
                        required>
                        @foreach ($employees as $employee)
                            <x-flux::select.option :value="$employee->id">{{ $employee->first_name .' '.$employee->last_name }}</x-flux::select.option>
                        @endforeach

                    </x-flux::select>
                    <x-flux::select
                        wire:model.defer="type"
                        label="{{ __('Leave Type') }}"
                        placeholder="{{ __('Select Leave Type') }}"
                        required
                    >
                        @foreach ($leaveTypes as $leaveType)
                            <x-flux::select.option :value="$leaveType">{{ ucfirst($leaveType) }}</x-flux::select.option>
                        @endforeach
                    </x-flux::select>
                </div>

                <div class="flex gap-4">
                    <x-flux::input
                        wire:model.defer="start_date"
                        type="date"
                        label="{{ __('Start Date') }}"
                        required
                        class="flex-1 w-full"
                    />
                    <x-flux::input
                        wire:model.defer="end_date"
                        type="date"
                        label="{{ __('End Date') }}"
                        required
                        class="flex-1"
                    />
                    <x-flux::select
                        wire:model="status"
                        label="{{ __('Status') }}"
                        placeholder="{{ __('Select Status') }}"
                        required
                    >
                        <x-flux::select.option value="pending">{{ __('Pending') }}</x-flux::select.option>
                        <x-flux::select.option value="approved">{{ __('Approved') }}</x-flux::select.option>
                        <x-flux::select.option value="rejected">{{ __('Rejected') }}</x-flux::select.option>
                    </x-flux::select>

                </div>
                <x-flux::textarea
                    wire:model.defer="reason"
                    label="{{ __('Reason') }}"
                    placeholder="{{ __('Enter reason for leave') }}"
                    rows="3"
                    required
                ></x-flux::textarea>
                <button type="submit" id="submit" x-ref="submit" class="hidden"></button>
                @if ($errors->any())
                    <div class="text-red-500 text-sm mt-2">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <x-slot:footer>
                <x-flux::button type="button" wire:click="$set('isOpen', false)">
                    {{ __('Cancel') }}
                </x-flux::button>
                <x-flux::button wire:click="store" variant="primary" class="ml-2">
                    {{ $selectedLeaveId ? __('Update Leave') : __('Create Leave') }}
                </x-flux::button>
            </x-slot:footer>
        </form>
    </x-modal>
</div>
