<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Application Logs</flux:heading>
            <flux:text class="mt-1">Recent entries from your Laravel application logs.</flux:text>
        </div>

        <div class="flex items-center gap-3">
            @can('logs.delete')
                <flux:button
                    variant="danger"
                    icon="trash"
                    wire:click="clearLogs"
                    wire:confirm="This permanently clears all Laravel log files. Do you want to continue?"
                >
                    Clear Logs
                </flux:button>
            @endcan

            <flux:button variant="primary" icon="arrow-path" wire:click="refreshLogs" wire:loading.attr="disabled">
                Refresh
            </flux:button>
        </div>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    <div class="overflow-hidden rounded-xl border border-neutral-700 bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-neutral-700 bg-neutral-800">
                    <tr>
                        <th class="w-28 px-6 py-4 font-medium text-neutral-300">Level</th>
                        <th class="w-52 px-6 py-4 font-medium text-neutral-300">Date</th>
                        <th class="px-6 py-4 font-medium text-neutral-300">Message</th>
                        <th class="w-28 px-6 py-4 text-right font-medium text-neutral-300">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-700">
                    @forelse ($logs as $index => $log)
                        <tr wire:key="log-{{ $index }}-{{ md5($log['date'].$log['message']) }}" class="transition hover:bg-neutral-800/70">
                            <td class="px-6 py-4">
                                <flux:badge size="sm" :variant="match ($log['level']) {
                                    'emergency', 'alert', 'critical', 'error' => 'danger',
                                    'warning' => 'warning',
                                    default => 'outline',
                                }">
                                    {{ strtoupper($log['level']) }}
                                </flux:badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-neutral-400">{{ $log['date'] }}</td>
                            <td class="max-w-2xl px-6 py-4 text-neutral-200">{{ \Illuminate\Support\Str::limit(str_replace("\n", ' ', $log['message']), 120) }}</td>
                            <td class="px-6 py-4 text-right">
                                <flux:button size="sm" variant="ghost" wire:click="viewLog({{ $index }})">View</flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <flux:icon name="document-text" class="mx-auto mb-3 size-10 text-neutral-500" />
                                <flux:heading size="sm">No log entries found</flux:heading>
                                <flux:text class="mt-1">There are no readable entries in the recent application log files.</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal name="log-details" wire:model="showDetails" @close="closeDetails" class="w-full max-w-4xl">
        @if ($selectedLog)
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">Log Entry</flux:heading>
                    <flux:subheading class="mt-1">{{ $selectedLog['date'] }}</flux:subheading>
                </div>

                <flux:badge :variant="match ($selectedLog['level']) {
                    'emergency', 'alert', 'critical', 'error' => 'danger',
                    'warning' => 'warning',
                    default => 'outline',
                }">
                    {{ strtoupper($selectedLog['level']) }}
                </flux:badge>

                <pre class="max-h-[55vh] overflow-auto whitespace-pre-wrap rounded-lg border border-neutral-700 bg-neutral-950 p-4 text-sm text-neutral-200">{{ $selectedLog['message'] }}</pre>

                <div class="flex justify-end">
                    <flux:button variant="primary" wire:click="closeDetails">Close</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
