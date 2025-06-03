<!-- resources/views/livewire/payroll-processing.blade.php -->
<div class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Payroll Processing</h1>
            <p class="text-sm text-gray-500">Process payroll for the selected employees.</p>
        </div>

        <!-- Payroll Period Selection -->
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
                <button wire:click="loadEmployees"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Load Employees
                </button>
            </div>
        </div>

        <!-- Employee Selection and Summary -->
        <div x-data="{
            tab: 'employees',

        }">
            <div class="flex gap-2 pb-2" role="tablist">
                <button
                    id="employees-tab"
                    type="button"
                    role="tab"
                    aria-label="Employees"
                    aria-selected="true"
                    @click="tab = 'employees'"
                    :class="tab === 'employees' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="rounded-full border shadow-md px-4 py-2">Employees
                </button>
                <button
                    id="payrolls-tab"
                    type="button"
                    role="tab"
                    aria-label="payrolls"
                    aria-selected="false"
                    @click="tab = 'payrolls'"
                    :class="tab === 'payrolls' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="rounded-full border shadow-md px-4 py-2">Payrolls
                </button>
            </div>

            <div x-show="tab === 'employees'" class="bg-white shadow-md rounded-lg overflow-x-auto" role="tabpanel" aria-labelledby="employees-tab">
                @if($employees->isNotEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <caption class="text-lg p-4 font-medium text-start bg-gray-50 text-gray-900">Select Employees</caption>
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employees as $employee)
                        <tr wire:key="employee-{{ $employee->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:model="selectedEmployees" value="{{ $employee->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $employee->department->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($employee->base_salary, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="showDeductions({{ $employee->id }})"
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View Deductions
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($employee->base_salary - $employee->total_deductions, 2) }}
                            </td>
                        </tr>

                        <!-- Deductions Detail Row -->
                        @if($showingDeductions == $employee->id)
                        <tr class="bg-gray-50">
                            <td colspan="6" class="px-6 py-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Deductions for {{ $employee->first_name }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach($employee->deductions as $deduction)
                                    <div class="border rounded-md p-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium">{{ $deduction->name }}</span>
                                            <span class="text-sm text-gray-600">
                                                {{ $deduction->pivot->amount }}
                                                @if($deduction->calculation_type == 'percentage')% @endif
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $deduction->description }}
                                        </div>
                                    </div>
                                    @endforeach
                                    <div>
                                        <button class="border rounded-md p-3 bg-gray-100 hover:bg-gray-200 w-full text-center"
                                                wire:click="createDeduction()">
                                            <span class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                Add Deductions
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                @endif
                @if( count($selectedEmployees) && $periodStart && $periodEnd)
                <!-- Process Payroll Button -->
                <div class="flex justify-end mt-4">
                    <button wire:click="processPayroll"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <span wire:loading.remove>Process Payroll</span>
                        <span wire:loading>
                            Processing...
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    Select a date range and load employees to begin payroll processing
                </div>
                @endif
            </div>
            <div x-show="tab === 'payroll'"
                class="bg-white shadow-md rounded-lg overflow-x-auto"
                role="tabpanel" aria-labelledby="tab1">
                @if($payrolls->isNotEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payrolls as $payroll)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payroll->employee->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payroll->period_start->format('M d, Y') }} - {{ $payroll->period_end->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($payroll->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $payroll->status === 'processed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('payroll.show', $payroll->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Export</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            <p>{{ session('message') }}</p>
        </div>
        @endif

        <!-- Recent Payrolls Section -->
        @if($recentPayrolls->isNotEmpty())
        <div class="mt-12">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Payrolls</h3>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentPayrolls as $payroll)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payroll->period_start->format('M d, Y') }} - {{ $payroll->period_end->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payroll->employees_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($payroll->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $payroll->status === 'processed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('payroll.show', $payroll->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Export</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    <!-- Deduction Management Modal -->
    <x-modal wire:show="showDeductionModal" maxWidth="lg" title="{{ $editingDeduction ? 'Edit Deduction' : 'Add New Deduction' }}">

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="deductionName">Deduction Name</label>
                <input id="deductionName" wire:model="deduction.name" id="deductionName" type="text" class="mt-1 block w-full" />
                @error('deduction.name')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>

            <div>
                <label for="deductionType">Type</label>
                <select wire:model="deduction.type" id="deductionType" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="tax">Tax</option>
                    <option value="benefits">Benefits</option>
                    <option value="voluntary">Voluntary</option>
                    <option value="loan">Loan</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="calculationType">Calculation Type</label>
                    <select wire:model="deduction.calculation_type" id="calculationType" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="fixed">Fixed Amount</option>
                        <option value="percentage">Percentage</option>
                    </select>
                    @error('deduction.calculation_type')
                        <span class="text-xs text-red-500">{{$message}}</span>
                    @enderror
                </div>

                <div>
                    <label for="amount">Amount</label>
                    <input wire:model="deduction.default_amount" id="amount" type="number" step="0.01" class="mt-1 block w-full" />
                    @error('deduction.default_amount')
                        <span class="text-xs text-red-500">{{$message}}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description">Description</label>
                <textarea wire:model="deduction.description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('deduction.description')
                    <span class="text-xs text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <x-slot name="footer">
            <button wire:click="$toggle('showDeductionModal')" class="ml-4 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Cancel
            </button>

            <button class="ml-2" wire:click.prevent="saveDeduction" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                {{ $editingDeduction ? 'Update' : 'Save' }}
            </button>
        </x-slot>
    </x-modal>
</div>
