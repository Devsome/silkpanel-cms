<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold font-headline gp-text-primary uppercase tracking-widest">
            {{ e($title) }}
        </h2>
    </div>

    @if (!$configured)
        <div class="text-center py-12">
            <p class="gp-text-on-surface-variant">{{ __('ranking.custom_not_configured') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto gp-card gp-ghost-border">
            <table class="min-w-full divide-y" style="border-color: rgba(77, 70, 53, 0.2);">
                <thead class="gp-card-high">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium gp-text-on-surface-variant uppercase tracking-wider">
                            #</th>
                        @foreach ($columns as $col)
                            <th
                                class="px-4 py-3 text-left text-xs font-medium gp-text-on-surface-variant uppercase tracking-wider">
                                {{ e($col['label']) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: rgba(77, 70, 53, 0.2);">
                    @foreach ($rows as $row)
                        @php $rank = $startRank + $loop->index; @endphp
                        <tr class="transition-colors hover:gp-card-high"
                            style="background-color: var(--gp-surface-container);">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium gp-text-outline">
                                {{ $rank }}</td>
                            @foreach ($columns as $col)
                                <td class="px-4 py-3 whitespace-nowrap text-sm gp-text-on-surface">
                                    {{ e((string) ($row->{$col['column']} ?? '—')) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($paginate && $rows->hasPages())
            <div class="mt-4">{{ $rows->links() }}</div>
        @endif
    @else
        <div class="text-center py-12">
            <p class="gp-text-on-surface-variant">{{ __('ranking.no_data') }}</p>
        </div>
    @endif
</div>
