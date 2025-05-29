<div>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Employee Management</h1>
            <div>
                <input wire:model.live="search" type="text" placeholder="Search employees..." class="px-4 py-2 border rounded-md">
                <button wire:click="create" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Add Employee
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <div
            x-data="{ sortField: @entangle('sortField'), sortDirection: @entangle('sortDirection') }"
            class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <button wire:click="sort('employee_id')" class="flex items-center text-gray-500 hover:text-gray-900">
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
                            <button wire:click="sort('email')" class="flex items-center text-gray-500 hover:text-gray-900">
                                Email
                                <flux:icon.chevron-up-down />
                            </button>
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <button wire:click="sort('phone')" class="flex items-center text-gray-500 hover:text-gray-900">
                                Phone
                                <flux:icon.chevron-up-down />
                            </button>
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <button wire:click="sort('base_salary')" class="flex items-center text-gray-500 hover:text-gray-900">
                                <flux:icon.chevron-up-down />
                            </button>
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $employee->employee_id }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $employee->email }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $employee->phone }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ number_format($employee->base_salary, 2) }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <button wire:click="edit({{ $employee->id }})" class="text-blue-600 hover:text-blue-900">
                                <flux:icon.square-pen />
                            </button>
                            <button wire:click="set('confirmDelete', {{$employee->id}})" class="text-red-600 hover:text-red-900 ml-4">
                                <flux:icon.trash-2 />
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-3 bg-white border-t border-gray-200">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
    <!-- Modal -->
    <x-modal wire:show="isOpen" maxWidth="2xl" title="{{ $employeeId ? 'Edit Employee' : 'Add Employee' }}">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <form>
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input wire:model="firstName" id="first_name" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('firstName')
                            <span class="text-xs text-red-500">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input wire:model="lastName" id="last_name" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('lastName')
                            <span class="text-xs text-red-500">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                        <input wire:model="employee_id" id="employee_id" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('employee_id')
                            <span class="text-xs text-red-500">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label for="base_salary" class="block text-sm font-medium text-gray-700">Base Salary</label>
                        <input wire:model="baseSalary" id="base_salary" type="number" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input wire:model="email" id="email" type="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <span class="text-xs text-red-500">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input wire:model="phone" id="phone" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <span class="text-xs text-red-500">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input wire:model="dateOfBirth" id="date_of_birth" type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('dateOfBirth')
                            <span class="text-xs text-red-500">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                        <input wire:model="hireDate" type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                        <select wire:model="department" id="department"
                            class="mt-1 block w-full border-r-10 outline outline-gray-300 border-transparent rounded-md shadow-sm py-2 px-3 focus:outline-blue-500 focus:ring-blue-500">
                            <option value="">Select Department</option>
                            @foreach ($departments as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                        <select wire:model="position" id="position"
                            class="mt-1 block w-full border-r-10 outline outline-gray-300 border-transparent rounded-md shadow-sm py-2 px-3 focus:outline-blue-500 focus:ring-blue-500">
                            <option value="">Select Position</option>
                            @foreach ($positions as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <label for="shift" class="block text-sm font-medium text-gray-700">Shift</label>
                        <select wire:model="shift" id="shift"
                            class="mt-1 block w-full border-r-10 outline outline-gray-300 border-transparent rounded-md shadow-sm py-2 px-3 focus:outline-blue-500 focus:ring-blue-500">
                            <option value="">Select Shift</option>
                            @foreach ($shifts as $item)
                                <option value="{{$item->id}}">{{$item->name}} ({{ \Carbon\Carbon::parse($item->time_in)->format('h:i A')}} - {{\Carbon\Carbon::parse($item->time_out)->format('h:i A')}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <x-slot name="footer">
            <button wire:click.prevent="store" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                {{ $isEdit ? 'Update' : 'Add New' }}
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Delete Employee</h3>
                    <p>Are you sure you want to delete this employee?</p>
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
