<div>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Attendance Tracking</h1>
            <div>
                <input wire:model.live="search" type="text" placeholder="Search employees..." class="px-4 py-2 border rounded-md">
                <button wire:click="create" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Add Attendance
                </button>
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
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('periodStart') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="period_end" class="block text-sm font-medium text-gray-700">Period End</label>
                <input wire:model="periodEnd" type="date" id="period_end"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('periodEnd') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-end">
                <button wire:click="$refresh"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Load Attendance
                </button>
            </div>
        </div>

        <div
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
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_in)->format('M d, Y h:i A') : '-' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('M d, Y h:i A') : '-' }}
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
        </div>
    </div>
    <!-- Modal -->
    <x-modal wire:show="isOpen" maxWidth="2xl" title="{{ $employeeId ? 'Edit Attendance' : 'Add Attendance' }}">
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
            <button wire:click="closeModal" class="ml-4 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Cancel
            </button>
        </x-slot>
    </x-modal>
    {{-- Confirm Delete --}}
    <div wire:show="confirmDelete" class="fixed z-30 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div wire:click="closeModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Delete Attendance</h3>
                    <p>Are you sure you want to delete this attendance?</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click.prevent="delete" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button wire:click.prevent="$set('confirmDelete', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
