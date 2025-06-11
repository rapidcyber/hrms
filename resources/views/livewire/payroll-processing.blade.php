<!-- resources/views/livewire/payroll-processing.blade.php -->
<div class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Payroll Processing</h1>
                <p class="text-sm text-gray-500">Process payroll for the selected employees.</p>
            </div>
            <div>
                <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search employees..." clearable />
                {{-- <input wire:model.live="search" type="search" placeholder="Search employees..." class="px-4 py-2 bg-white border rounded-md"> --}}
                {{-- <button wire:click="create" @click="$refresh()" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Add Attendance
                </button> --}}
            </div>
        </div>

        <!-- Payroll Period Selection -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <x-flux::input
                    type="date"
                    wire:model="periodStart"
                    label="Cut-off Start"/>
            </div>
            <div>
                <flux:field>
                    <x-flux::input
                        type="date"
                        wire:model="periodEnd"
                        label="Cut-off End" />
                    <flux:error
                        name="periodEnd"
                        class="hidden"/>
                </flux:field>
            </div>
            <div>
                <div>&nbsp;</div>
                <flux:button
                    icon="list-restart"
                    variant="primary"
                    wire:click="reloadEmployees"
                >
                    Load Employees
                </flux:button>
            </div>
        </div>
        <!-- Success Message -->
        @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            <p>{{ session('message') }}</p>
        </div>
        @endif
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
                    @click="tab = 'employees';$wire.loadEmployees()"
                    :class="tab === 'employees' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="rounded-full border shadow-md px-4 py-2">Employees
                </button>
                @if( count($selectedEmployees) && $periodStart && $periodEnd)
                <!-- Process Payroll Button -->

                    <button wire:click="processPayroll"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-green-600 text-white rounded-full hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <span wire:loading.remove>Process Payroll</span>
                        <span wire:loading>
                            Processing...
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                @endif
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
                @if( count($selectedPayrolls))
                <button
                    id="delete-all"
                    type="button"
                    wire:click="$set('confirmDeleteAll', 1)"
                    class="rounded-full text-white border bg-red-500 shadow-md px-4 py-2">Delete All
                </button>
                @endif
            </div>

            <div x-show="tab === 'employees'" class="bg-white shadow-md rounded-lg overflow-x-auto" role="tabpanel" aria-labelledby="employees-tab">
                <x-flux.table
                    x-data="{ showingDeductions: false }"
                    :data="$employees"
                    :striped="true"
                    :hover="true"
                    :bordered="false"
                    responsive
                    id="attendance-table"
                    class="mb-6 min-w-full divide-y divide-gray-200"
                    wire:loading.class="opacity-50"
                    wire:target="processPayroll, showDeductions, deleteDeduction, editDeduction, toggleSelectAll, viewPayroll">
                    <x-slot name="head">
                        <x-flux.table.heading>
                            <flux:tooltip content="Select All Employees" placement="top">
                                <input type="checkbox" id="select-all-employees" wire:model="selectAll" wire:click="toggleSelectAll">
                            </flux:tooltip>
                        </x-flux.table.heading>
                        <x-flux.table.heading sortable sort-by="first_name" direction="{{$sortDirection}}">Employee</x-flux.table.heading>
                        <x-flux.table.heading>Overtime</x-flux.table.heading>
                        <x-flux.table.heading>Base Salary</x-flux.table.heading>
                        <x-flux.table.heading>Shift</x-flux.table.heading>
                        <x-flux.table.heading>Actions</x-flux.table.heading>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($employees as $employee)
                            <x-flux.table.row :even="$loop->even">
                                <x-flux.table.cell>
                                    <input type="checkbox" id="employee-{{ $employee->id }}" wire:model.live="selectedEmployees" value="{{ $employee->id }}">
                                </x-flux.table.cell>
                                <x-flux.table.cell class="font-medium text-gray-900">
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    <span class="{{ $overtimes[$employee->id]['status'] ? 'text-purple-600' : 'text-red-600' }}">{{ $overtimes[$employee->id]['hours'] ?? 0 }} hours</span>
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    {{ number_format($employee->base_salary, 2) }}
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    {{ $employee->shift->name }}
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    <flux:button
                                        size="xs"
                                        variant="outline"
                                        icon="eye"
                                        wire:click="showDeductions({{ $employee->id }})"
                                        @click="showingDeductions = showingDeductions === {{$employee->id}} ? false : {{$employee->id}}"
                                        @keyup.escape.window="showingDeductions = false"
                                    >
                                        Summary
                                    </flux:button>
                                </x-flux.table.cell>
                            </x-flux.table.row>

                            <!-- Deductions Detail Row -->
                            <x-flux.table.row
                                :even="$loop->even"
                                wire:loading.class="opacity-50"
                                x-show="showingDeductions === {{$showingDeductions = $employee->id}}"
                                wire:target="showDeductions, editDeduction, deleteDeduction">
                                <x-flux.table.cell colspan="6" class="px-6 py-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Summary for {{ $employee->first_name }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="border bg-gray-200 rounded-md p-3">
                                            <div class="flex justify-between gap-1 items-center">
                                                <span class="text-sm font-medium">Over Time: </span>
                                                {{-- @php($hours = $overtimes[$employee->id]['hours'] ?? 0)
                                                @php($status = $overtimes[$employee->id]['status'] ?? false) --}}
                                                <span
                                                    class="text-sm text-[#3F507F] flex justify-between flex-1 gap-1 items-center">
                                                    <input type="number" wire:model.live="overtimes.{{ $employee->id }}.hours" step="0.01" class="border border-gray-300 rounded-md p-1 text-right w-full bg-white" />
                                                    <span>hours</span>
                                                    <flux:button
                                                        size="xs"
                                                        variant="outline"
                                                        {{-- @click="status=!status;$wire.approveOvertime({{ $employee->id }}, hours, status)" --}}
                                                        wire:click="approveOvertime({{ $employee->id }})"
                                                        class="w-fit">
                                                        <span>{{ $overtimes[$employee->id]['status'] ? 'DENY' : 'APPROVE' }}</span>
                                                    </flux:button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="border rounded-md p-3 bg-gray-200">
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium">Lates</span>
                                                <span class="text-sm text-red-600">
                                                    {{ number_format($summary['lates'] ?? 0, 2) }} hours
                                                </span>
                                            </div>
                                        </div>
                                        @foreach ($employee->deductions as $deduction)
                                        <div class="border bg-white rounded-md p-3 flex gap-2 items-center justify-between">
                                            <div class="flex gap-4">
                                                <span class="text-sm font-medium capitalize">{{ $deduction->type }}:</span>
                                                <span class="text-sm text-gray-600">
                                                    {{ $deduction->amount }}
                                                </span>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:button
                                                    size="xs"
                                                    variant="outline"
                                                    wire:click="editDeduction({{ $deduction->id }})"
                                                    icon="square-pen"
                                                >

                                                </flux:button>
                                                <flux:button
                                                    size="xs"
                                                    variant="danger"
                                                    wire:click="deleteDeduction({{ $deduction->id }})"
                                                    class="ml-2"
                                                    icon="trash"
                                                >
                                                </flux:button>
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
                                </x-flux.table.cell>
                            </x-flux.table.row>



                        @empty
                            <x-flux.table.row>
                                <x-flux.table.cell colspan="6" class="text-center text-gray-500">
                                    No employees found.
                                </x-flux.table.cell>
                            </x-flux.table.row>
                        @endforelse
                    </x-slot>
                </x-flux.table>
            </div>
            <div x-show="tab==='payrolls'">
                <div class="flex justify-end pb-2 gap-4">
                    <div class="text-xl">Grand Total: &#8369; <strong class="font-bold text-blue-500">{{number_format($payrolls->sum('net_salary'),2)}}</strong></div>
                    <flux:button
                        size="sm"
                        variant="outline"
                        icon="printer"
                        wire:click="printPayrolls()"
                    >
                        Print Payslips
                    </flux:button>
                </div>
                <x-flux.table
                    :data="$payrolls"
:striped="true"
                    :hover="true"
                    :bordered="false"
                    responsive
                    id="attendance-table"
                    class="mb-6 min-w-full divide-y divide-gray-200"
                    wire:loading.class="opacity-50"
                    wire:target="deleteAllPayroll,toggleSelectAllPayroll, deletePayroll, viewPayroll,printPayrolls,downloadPayroll">
                    <x-slot name="head">
                        <x-flux.table.heading>
                                <input type="checkbox" id="select-all-payroll" wire:model.live="selectAllPayroll" wire:click="toggleSelectAllPayroll">
                        </x-flux.table.heading>
                        <x-flux.table.heading>Employee</x-flux.table.heading>
                        <x-flux.table.heading>Period</x-flux.table.heading>
                        <x-flux.table.heading>Total Amount</x-flux.table.heading>
                        <x-flux.table.heading>Status</x-flux.table.heading>
                        <x-flux.table.heading>Actions</x-flux.table.heading>
                    </x-slot>
                    <x-slot name="body">
                        @foreach($payrolls as $payroll)
                            <x-flux.table.row :even="$loop->even">
                                <x-flux.table.cell>
                                    <input type="checkbox" id="payroll-{{ $payroll->id }}" wire:model.live="selectedPayrolls" value="{{ $payroll->id }}">
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    {{ \Carbon\Carbon::parse($payroll->period_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($payroll->period_end)->format('M d, Y') }}
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    {{ number_format($payroll->net_salary, 2) }}
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $payroll->status === 'processed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                </x-flux.table.cell>
                                <x-flux.table.cell>
                                    <flux:button
                                        size="xs"
                                        variant="outline"
                                        icon="eye"
                                        class="mr-2"
                                        wire:click="viewPayroll({{ $payroll->id }})"
                                    >
                                        View
                                    </flux:button>
                                    <flux:button
                                        size="xs"
                                        variant="danger"
                                        icon="trash"
                                        wire:click="deletePayroll({{ $payroll->id }})"
                                    >
                                        Delete
                                    </flux:button>
                                </x-flux.table.cell>
                            </x-flux.table.row>
                        @endforeach
                    </x-slot>
                </x-flux.table>
            </div>
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
                                {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($payroll->period_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($payroll->period_end)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($payroll->net_salary, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $payroll->status === 'processed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Export</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
    <!-- Deduction Management Modal -->
    <x-modal wire:show="showDeductionModal" maxWidth="lg" title="{{ $editingDeduction ? 'Edit Deduction' : 'Add New Deduction' }}">

        <div class="grid grid-cols-2 gap-4">

            <div>
                <x-flux::select
                    label="Type"
                    wire:model="deduction_type"
                    placeholder="Select Deduction Type..."
                    >
                    <x-flux::select.option value="cash-advance">Cash Advance</x-flux::option>
                    <x-flux::select.option value="loan">Salary Loan</x-flux::option>
                </x-flux::select>
            </div>

            <div>
                <x-flux::input
                    type="number"
                    label="Amount"
                    wire:model="deduction_amount"
                    placeholder="Enter amount"
                    />
            </div>

        </div>
        <x-slot name="footer">
            <flux:button wire:click="$toggle('showDeductionModal')">Cancel</flux:button>
            <flux:button class="ml-2" wire:click.prevent="saveDeduction" variant="primary">{{ $editingDeduction ? 'Update' : 'Save' }}</flux:button>
        </x-slot>
    </x-modal>
    <x-modal
        wire:show="showPayrollModal"
        maxWidth="lg"
        title="Payroll Details"
    >
        <div class="flex items-center bg-[#3F507F] p-2 text-white">
            <img class="size-20" src="{{url('/images/sp_logo.png')}}" alt="" srcset="">
            <div class="text-center flex-1">
                <div>
                    Serbsyong CongPleyto Movement
                </div>
                <div class="mt-2">
                    OFFICE PAYSLIP
                </div>
            </div>
            <img class="size-20" src="{{url('/images/hrp_logo.png')}}" alt="" srcset="">
        </div>
        @if($viewingPayroll)
            <table class="w-full">
            <thead>
                <tr>
                    <th style="width: 35%"></th>
                    <th style="width: 65%"></th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>Date:</td>
                    <td>
                        {{\Carbon\Carbon::parse($viewingPayroll->period_start)->format('M j, Y')}} - {{\Carbon\Carbon::parse($viewingPayroll->period_end)->format('M j, Y')}}
                    </td>
                </tr>
                <tr>
                    <td>Name:</td>
                    <td class="text-white bg-[#3F507F] px-3 py-2">
                        {{$viewingPayroll->employee->first_name}} {{$viewingPayroll->employee->last_name}}
                    </td>
                </tr>
                <tr>
                    <td>Basic Payroll:</td>
                    <td style="font-weight: bold;color:#3F507F">₱ {{ number_format($viewingPayroll->employee->base_salary / 2, 2) }}</td>
                </tr>
                <tr>
                    <td>Over Time:</td>
                    <td style="font-weight: bold;color:#3F507F">&#8369; {{ number_format($viewingPayroll->overtime_pay, 2) }}</td>
                </tr>
                <tr>
                    <td class="pr-3">Sunday Overtime</td>
                    <td style="font-weight: bold;color:#3F507F">&#8369; 0.00</td>
                </tr>
                <tr>
                    <td>Lates</td>
                    <td style="color:red">&#8369; {{ number_format($viewingPayroll->lates, 2) }}</td>
                </tr>
                <tr>
                    <td>Under Time:</td>
                    <td style="color:red">&#8369; 0.00</td>
                </tr>
                @forelse ($viewingPayroll->deductions as $deduction)
                <tr>
                    <td>{{ ucfirst($deduction->type)}}: </td>
                    <td style="color:red;font-weight:bold">₱ {{number_format($deduction->amount,2)}}</td>
                </tr>
                @empty
                    <tr>
                        <td>Loan: </td>
                        <td style="color:red;font-weight:bold">₱ 0.00</td>
                    </tr>
                    <tr>
                        <td>Cash Advance: </td>
                        <td style="color:red;font-weight:bold">₱ 0.00</td>

                    </tr>
                @endforelse
                <tr>
                    <td>Absents</td>
                    <td style="color:red">₱ {{ number_format($viewingPayroll->absents, 2) }}</td>
                </tr>
            </tbody>
            <tfoot style="background-color:yellowgreen">
                <tr>
                    <td class="text-xl px-3 py-2"><strong>Total:</strong></td>
                    <td><strong class="text-xl">₱ {{ number_format($viewingPayroll->net_salary, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
        <x-slot name="footer">
            <flux:button wire:click="$set('showPayrollModal', false)">Close</flux:button>
            <flux:button wire:click="downloadPayroll({{$viewingPayroll->id}})">Download</flux:button>
        </x-slot>
        @endif

    </x-modal>

    {{-- Confirm Delete all --}}
    <x-modal wire:show="confirmDeleteAll" title="Delete All Payrolls">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            Are you sure you want to delete all selected payroll? This action cannot be undone.
        </div>
        <x-slot name="footer">
            <flux:button wire:click="$set('confirmDeleteAll', false)">Close</flux:button>
            <flux:button wire:click="deleteAllPayroll()">Delete All</flux:button>
        </x-slot>
    </x-modal>
</div>
