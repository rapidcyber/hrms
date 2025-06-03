<div>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Attendance Tracking</h1>
            <div class="flex gap-2">
                <div>
                    <input wire:model.live="search" type="text" placeholder="Search employees..." class="px-4 py-2 bg-white border rounded-md">
                    <button wire:click="create" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Attendance
                    </button>
                </div>
                <x-flux::button icon="import">Import</x-flux::button>
                <x-flux::button variant="primary" icon="refresh-ccw">Sync</x-flux::button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <label for="period_start" class="block text-sm font-medium text-gray-700">Period Start</label>
                <input wire:model="periodStart" type="date" id="period_start"
                       class="mt-1 block w-full border bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('periodStart') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="period_end" class="block text-sm font-medium text-gray-700">Period End</label>
                <input wire:model="periodEnd" type="date" id="period_end"
                       class="mt-1 block w-full border bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('periodEnd') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-end">
                <button wire:click="$refresh"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Load Attendance
                </button>
            </div>
        </div>

        {{-- <div
            x-data="{ sortField: @entangle('sortField'), sortDirection: @entangle('sortDirection') }"
            class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <button wire:click="sort('employees.employee_id')" class="flex items-center text-gray-500 hover:text-gray-900">
                                Employee ID
                                <flux:icon.chevron-up-down />
                            </button>
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <button wire:click="sort('first_name')" class="flex items-center text-gray-500 hover:text-gray-900">
                                Name
                                <flux:icon.chevron-up-down />
                            </button>
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <button wire:click="sort('date')" class="flex items-center text-gray-500 hover:text-gray-900">
                                Date
                                <flux:icon.chevron-up-down />
                            </button>
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <button wire:click="sort('check_in')" class="flex items-center text-gray-500 hover:text-gray-900">
                                Check-in
                                <flux:icon.chevron-up-down />
                            </button>
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <button wire:click="sort('check_out')" class="flex items-center text-gray-500 hover:text-gray-900">
                                Check-out
                                <flux:icon.chevron-up-down />
                            </button>
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $attendance->employee->employee_id }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $attendance->in_1 ? \Carbon\Carbon::parse($attendance->in_1)->format('h:i A') : '-' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            @php($checkout = $attendance->out_3 ?? $attendance->out_2 ?? $attendance->out_1 ?? null)

                            {{ $checkout ? \Carbon\Carbon::parse($checkout)->format('h:i A') : '-' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $attendance->status }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <button wire:click="edit({{ $attendance->id }})" class="text-blue-600 hover:text-blue-900">
                                <flux:icon.square-pen />
                            </button>
                            <button wire:click="set('confirmDelete', {{$attendance->id}})" class="text-red-600 hover:text-red-900 ml-4">
                                <flux:icon.trash-2 />
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-3 bg-white border-t border-gray-200">
                {{ $attendances->links() }}
            </div>
        </div> --}}

        <div class="bg-white shadow w-full overflow-x-auto rounded-lg p-6">
            <x-flux.table
                :striped="true"
                :hover="true"
                :bordered="false"
                responsive
                id="attendance-table"
                class="mb-6"
                wire:loading.class="opacity-50"
                wire:target="sort, delete, store"
            >
                <x-slot:head>
                    <x-flux.table.heading wire:click="sort('employee_id')" sortable sort-by="employee_id" direction="{{ $sortDirection['employee_id'] }}" width="15%">
                        {{ __('Employee ID') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading wire:click="sort('first_name')" sortable sort-by="first_name" direction="{{ $sortDirection['first_name'] }}" width="30%">
                        {{ __('Name') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading wire:click="sort('date')" sortable sort-by="date" direction="{{ $sortDirection['date'] }}" width="20%">
                        {{ __('Date') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading width="20%">
                        {{ __('Time-in') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading width="20%">
                        {{ __('Time-out') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading width="20%">{{ __('Actions') }}</x-flux.table.heading>
                </x-slot:head>

                <x-slot:body>
                    @foreach($attendances as $attendance)
                        <x-flux.table.row :even="$loop->even">
                            <x-flux.table.cell>{{ $attendance->employee->employee_id }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ \Carbon\Carbon::parse($attendance->date)->format('F j, Y') }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $attendance->in_1 ? \Carbon\Carbon::parse($attendance->in_1)->format('h:i A') : '-' }}</x-flux.table.cell>
                            <x-flux.table.cell>
                                @php($checkout = $attendance->out_3 ?? $attendance->out_2 ?? $attendance->out_1 ?? null)
                                {{ $checkout ? \Carbon\Carbon::parse($checkout)->format('h:i A') : '-' }}
                            </x-flux.table.cell>
                            <x-flux.table.cell class="text-right">
                                <x-flux::button wire:click="edit({{ $attendance->id }})" icon="square-pen" secondary>
                                    {{ __('Edit') }}
                                </x-flux::button>
                                <x-flux::button wire:click="$set('cofirmDelete', {{ $attendance->id }})" icon="trash" variant="danger">
                                    {{ __('Delete') }}
                                </x-flux::button>
                            </x-flux.table.cell>
                        </x-flux.table.row>
                    @endforeach
                    @if($attendances->isEmpty())
                        <x-flux.table.row>
                            <x-flux.table.cell colspan="3" class="text-center text-gray-500">
                                {{ __('No attendances found.') }}
                            </x-flux.table.cell>
                        </x-flux.table.row>
                    @endif
                </x-slot:body>
            </x-flux.table>
        </div>
        @if($attendances->hasPages())
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
    <!-- Modal -->
    <x-modal wire:show="isOpen" maxWidth="2xl" title="{{ $attendanceId ? 'Edit' : 'Add' }} Attendance">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Select Employee</label>
                    <select wire:model="employeeId" id="employee_id"
                        class="mt-1 block w-full border-r-10 outline outline-gray-300 border-transparent rounded-md shadow-sm py-2 px-3 focus:outline-blue-500 focus:ring-blue-500">
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                    @error('employeeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-3">
                    <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in</label>
                    <input wire:model="checkIn" type="datetime-local" id="check_in"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('checkIn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-3">
                    <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out</label>
                    <input wire:model="checkOut" type="datetime-local" id="check_out"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('checkOut') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <x-slot name="footer">
            <button wire:click.prevent="store" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                {{ $attendanceId ? 'Update' : 'Add New' }}
            </button>
            <button wire:click="$set('isOpen', false)" class="ml-4 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Cancel
            </button>
        </x-slot>
    </x-modal>
    {{-- Confirm Delete --}}
    <x-modal wire:show="confirmDelete" title="Delete Attendance">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <p>Are you sure you want to delete this attendance?</p>
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
