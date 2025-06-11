<div>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Attendance Tracking</h1>
            <div class="flex gap-2">
                <div>
                    <input wire:model.live="search" type="search" placeholder="Search employees..." class="px-4 py-2 bg-white border rounded-md">
                    <button wire:click="create" @click="$refresh()" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Attendance
                    </button>
                </div>
                <input
                    type="file" name="file" wire:model="file"
                    x-on:livewire-upload-finish="$wire.fileSelected()"
                    id="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv" class="hidden">

                <x-flux::button @click="document.getElementById('file').click()" icon="import">Import</x-flux::button>
                <x-flux::button variant="primary" icon="refresh-ccw" wire:click="syncBiometricData">Sync</x-flux::button>
            </div>
        </div>
        @if ($errors->any())
            <div class="mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
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
            <div class="flex items-end gap-2">
                <button wire:click="$refresh"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Load Attendance
                </button>
                <x-flux::button wire:click="export" icon="file-up">Export</x-flux::button>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif
        <div class="bg-white shadow w-full overflow-x-auto rounded-lg p-6">
            <x-flux.table
                :striped="true"
                :hover="true"
                :bordered="false"
                responsive
                id="attendance-table"
                class="mb-6"
                wire:loading.class="opacity-50"
                wire:target="sort, delete, store, syncBiometricData, import"
            >
                <x-slot:head>
                    <x-flux.table.heading sortable sort-by="employees.first_name" direction="{{ $sortDirection['employees.first_name'] }}" width="30%">
                        {{ __('Name') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading sortable sort-by="date" direction="{{ $sortDirection['date'] }}" width="20%">
                        {{ __('Date') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading width="20%">
                        {{ __('Time-in') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading width="20%">
                        {{ __('Time-out') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading width="20%">
                        {{ __('Hours Worked') }}
                    </x-flux.table.heading>
                    <x-flux.table.heading width="20%">{{ __('Actions') }}</x-flux.table.heading>
                </x-slot:head>

                <x-slot:body>
                    @foreach($attendances as $attendance)
                        <x-flux.table.row :even="$loop->even">
                            @php($checkIn = $attendance->in_1 ?? $attendance->in_2 ?? $attendance->in_3)
                            @php($checkout = $attendance->out_3 ?? $attendance->out_2 ?? $attendance->out_1 ?? null)
                            @php($date = \Carbon\Carbon::parse($attendance->date))
                            <x-flux.table.cell>{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $date->format('F j, Y') }} {{ $weekMap[ $date->dayOfWeek()]}}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $checkIn ? \Carbon\Carbon::parse($checkIn)->format('h:i A') : '-' }}</x-flux.table.cell>
                            <x-flux.table.cell>

                                {{ $checkout ? \Carbon\Carbon::parse($checkout)->format('h:i A') : '-' }}
                            </x-flux.table.cell>
                        <x-flux.table.cell>
                            {{$attendance->hours_worked}}
                        </x-flux.table.cell>
                            <x-flux.table.cell class="text-right">
                                <x-flux::button wire:click="edit({{ $attendance->id }})" icon="square-pen" secondary>
                                    {{ __('Edit') }}
                                </x-flux::button>
                                <x-flux::button wire:click="$set('confirmDelete', {{ $attendance->id }})" icon="trash" variant="danger">
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
                <div class="sm:col-span-2">
                    <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in</label>
                    <input wire:model="checkIn" wire:change="updateDate" type="datetime-local" id="check_in"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('checkIn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out</label>
                    <input wire:model="checkOut" type="datetime-local" id="check_out"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('checkOut') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model="status" id="status"
                        class="mt-2 block w-full border-r-10 outline outline-gray-300 border-transparent rounded-md shadow-sm py-2 px-3 focus:outline-blue-500 focus:ring-blue-500">
                        <option value="">-- Select Status --</option>
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="half-day">Half-day</option>
                        <option value="absent">Absent</option>
                        <option value="rest-day">Rest Day</option>
                    </select>
                    {{-- <input wire:model="status" type="text" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"> --}}
                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-6">
                    <x-flux::textarea
                    label="Remarks"
                    wire:model="remarks"
                    aria-placeholder="Additional Information">
                    </x-flux::textarea>
                </div>
            </div>
            @if ($errors->any())
                <div class="mb-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        <x-slot name="footer">
            <button wire:click="$set('isOpen', false)" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Cancel
            </button>
            <button wire:click.prevent="store" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                {{ $attendanceId ? 'Update' : 'Add New' }}
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
