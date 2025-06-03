<div class="relative">
    <div class="p-4">
        <h1 class="text-2xl font-bold">Activities</h1>
    </div>
    <div class="p-4">
        <div class="p-4 bg-white rounded-lg shadow">
            <x-flux.table>
                <x-slot:head>
                    <x-flux.table.heading>Date</x-flux.table.heading>
                    <x-flux.table.heading>User</x-flux.table.heading>
                    <x-flux.table.heading>Action</x-flux.table.heading>
                    <x-flux.table.heading>Description</x-flux.table.heading>
                </x-slot:head>
                <x-slot:body>
                    @forelse($activityLogs as $log)
                        <x-flux.table.row>
                            <x-flux.table.cell>{{ \Carbon\carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $log->user->name ?? 'System' }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $log->action }}</x-flux.table.cell>
                            <x-flux.table.cell>{{ $log->description }}</x-flux.table.cell>
                        </x-flux.table.row>
                    @empty
                        <x-flux.table.row>
                            <x-flux.table.cell colspan="4" class="text-center text-sm text-gray-500">
                                No activity logs found.
                            </x-flux.table.cell>
                        </x-flux.table.row>
                    @endforelse
                </x-slot:body>
            </x-flux.table>
        </div>
    </div>

</div>
