<!-- resources/views/livewire/employee-management.blade.php -->
<div>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Employee Management</h1>
            <div>
                <input wire:model="search" type="text" placeholder="Search employees..." class="px-4 py-2 border rounded-md">
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

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employee ID</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Phone</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Base Salary</th>
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
                            <button wire:click="edit({{ $employee->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                            <button wire:click="delete({{ $employee->id }})" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-3 bg-white border-t border-gray-200">
                {{ $employees->links() }}
            </div>
        </div>

        <!-- Modal -->
        @if($isOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ $employeeId ? 'Edit' : 'Create' }} Employee</h3>
                        <form>
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input wire:model="firstName" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input wire:model="lastName" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input wire:model="email" type="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input wire:model="phone" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input wire:model="dateOfBirth" type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                                    <input wire:model="hireDate" type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="base_salary" class="block text-sm font-medium text-gray-700">Base Salary</label>
                                    <input wire:model="baseSalary" type="number" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click.prevent="store" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
