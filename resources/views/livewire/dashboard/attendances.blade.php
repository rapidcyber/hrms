<div class="relative">
    <div class="p-4">
        <h1 class="text-2xl font-bold">Recent Attendance</h1>
    </div>
    <div class="p-4">
        <div class="p-4 bg-white rounded-lg shadow">
            <x-flux.table>
                <x-slot:head>
                    <x-flux.table.heading>Date</x-flux.table.heading>
                    <x-flux.table.heading>Employee</x-flux.table.heading>
                    <x-flux.table.heading>Time-in</x-flux.table.heading>
                    <x-flux.table.heading>Time-out</x-flux.table.heading>
                </x-slot:head>
                <x-slot:body>
                    @forelse($attendances as $attendance)
                        <x-flux.table.row>
                            <x-flux.table.cell>{{ \Carbon\carbon::parse($attendance->date)->format('F j, Y') }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $attendance->employee->first_name }} {{$attendance->employee->first_name}}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $attendance->in_1 ? \Carbon\Carbon::parse($attendance->in_1)->format('h:i A') : '-' }}</x-flux.table.cell>
                            <x-flux.table.cell>
                                @php($checkout = $attendance->out_3 ?? $attendance->out_2 ?? $attendance->out_1 ?? null)
                                {{ $checkout ? \Carbon\Carbon::parse($checkout)->format('h:i A') : '-' }}
                            </x-flux.table.cell>
                        </x-flux.table.row>
                    @empty
                        <x-flux.table.row>
                            <x-flux.table.cell colspan="4" class="text-center text-sm text-gray-500">
                                No Attendance found yet.
                            </x-flux.table.cell>
                        </x-flux.table.row>
                    @endforelse
                </x-slot:body>
            </x-flux.table>
        </div>
    </div>
</div>
