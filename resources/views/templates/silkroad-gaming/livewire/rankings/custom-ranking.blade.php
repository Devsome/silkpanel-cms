<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white uppercase tracking-widest">{{ e($title) }}</h2>
    </div>

    @if (!$configured)
        <div class="text-center py-12">
            <p class="mt-4 text-gray-500">{{ __('ranking.custom_not_configured') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto rounded-2xl border border-gray-800 bg-gray-900/50">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-gray-900/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        @foreach ($columns as $col)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ e($col['label']) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @foreach ($rows as $row)
                        @php $rank = $startRank + $loop->index; @endphp
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-500">
                                {{ $rank }}</td>
                            @foreach ($columns as $col)
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-200">
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
            <p class="text-gray-500">{{ __('ranking.no_data') }}</p>
        </div>
    @endif
</div>
