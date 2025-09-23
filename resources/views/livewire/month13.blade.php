<div class="min-h-screen">
    <div class="container min-h-screen mx-auto px-4">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold">13th Month Processing</h1>
                <p class="text-sm text-gray-500">Process 13th month salary for the selected employees.</p>
            </div>
            <div>
                <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search employees..." clearable />
            </div>
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

        <div class="flex flex-col" x-data="{ activeTab: 1 }">
            <!-- Buttons -->
            <div class="flex justify-center">
                <div
                    role="tablist"
                    class="max-[480px]:max-w-[180px] inline-flex flex-wrap justify-center bg-slate-200 rounded-[20px] p-1 mb-8 min-[480px]:mb-12"
                    @keydown.right.prevent.stop="$focus.wrap().next()"
                    @keydown.left.prevent.stop="$focus.wrap().prev()"
                    @keydown.home.prevent.stop="$focus.first()"
                    @keydown.end.prevent.stop="$focus.last()"
                >
                    <!-- Button #1 -->
                    <button
                        id="tab-1"
                        class="flex-1 text-sm font-medium h-8 px-4 rounded-2xl whitespace-nowrap focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300 transition-colors duration-150 ease-in-out"
                        :class="activeTab === 1 ? 'bg-white text-slate-900' : 'text-slate-600 hover:text-slate-900'"
                        :tabindex="activeTab === 1 ? 0 : -1"
                        :aria-selected="activeTab === 1"
                        aria-controls="tabpanel-1"
                        @click="activeTab = 1"
                        @focus="activeTab = 1"
                    >Employees</button>
                    <!-- Button #2 -->
                    <button
                        id="tab-2"
                        class="flex-1 text-sm font-medium h-8 px-4 rounded-2xl whitespace-nowrap focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300 transition-colors duration-150 ease-in-out"
                        :class="activeTab === 2 ? 'bg-white text-slate-900' : 'text-slate-600 hover:text-slate-900'"
                        :tabindex="activeTab === 2 ? 0 : -1"
                        :aria-selected="activeTab === 2"
                        aria-controls="tabpanel-2"
                        @click="activeTab = 2"
                        @focus="activeTab = 2"
                    >Processed</button>
                </div>
            </div>
            <div x-show="activeTab === 1" class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                        <button wire:click="sortBy('first_name')" class="flex items-center uppercase text-gray-500 hover:text-gray-900">
                                            Name <flux:icon.chevron-up-down variant="micro" />
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                        <button wire:click="sortBy('base_salary')" class="flex items-center uppercase text-gray-500 hover:text-gray-900">
                                            Basic Salary
                                            <flux:icon.chevron-up-down variant="micro" />
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 tracking-wider">
                                        Hired Since
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($employees as $employee)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($employee->base_salary) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <flux:button icon="sparkles" variant="primary" size="sm" wire:click="process({{ $employee->id }})"><b class="text-xs">PROCESS</b></flux:button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap" colspan="3">No employees found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="px-4 py-2">
                                        {{ $employees->links() }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div x-show="activeTab === 2">
                <div class="flex justify-end items-center p-2">
                    <flux:dropdown>
                        <flux:button icon:trailing="chevron-down">Export to</flux:button>
                        <flux:menu>
                            <flux:menu.item icon="table-cells">Excel</flux:menu.item>
                            <flux:menu.item icon="document">PDF</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($thirteenthPays as $item)
                                <tr>
                                <td class="px-4 py-2">{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->payment_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-2">{{ number_format($item->amount, 2) }}</td>
                                <td class="px-4 py-2 text-right">
                                    <flux:button icon="eye" variant="primary" size="sm"><b class="text-xs">VIEW</b></flux:button>
                                    <flux:button icon="trash" variant="danger" size="sm"><b class="text-xs">DELETE</b></flux:button>
                                </td>
                            </tr>
                            @empty

                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <x-modal wire:show="showProcessModal">
        <x-slot name="title">
            Process 13th Month Pay
        </x-slot>
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

            <flux:input wire:model="date" label="Date" type="date"  placeholder="YYYY-MM-DD" />
            <div class="mt-4 rounded-md shadow-sm -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);">
                <table class="min-w-full divide-y divide-gray-200">
                    <caption>Summary for the year {{\Carbon\Carbon::parse($date)->year}}</caption>
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                Daily Rate
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                Days Worked
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                Total Pay
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-2">{{$summary['daily_rate'] ?? 0}}</td></td>
                            <td class="px-6 py-2">{{$summary['days_worked'] ?? 0}}</td>
                            <td class="px-6 py-2" style="background-color:yellowgreen">{{ number_format($summary['total_pay'] ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>



        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <flux:button wire:click.prevent="$set('showProcessModal', false)" icon="x">
                    <b class="text-xs">CANCEL</b>
                </flux:button>
                <flux:button wire:click.prevent="save()" variant="primary" icon="check-circle">
                    <b class="text-xs">CONFIRM</b>
                </flux:button>
            </div>

        </x-slot>

    </x-modal>
    {{-- Loader spinner --}}
    <div wire:loading wire:target="save">
        <x-loader />
    </div>

</div>
