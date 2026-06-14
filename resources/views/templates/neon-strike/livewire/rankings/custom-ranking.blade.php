<div>
    <div class="mb-5">
        <h2
            class="text-sm font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
            {{ e($title) }}
        </h2>
    </div>

    @if (!$configured)
        <div class="py-12 text-center border border-zinc-800/60">
            <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">
                {{ __('ranking.custom_not_configured') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto border border-violet-500/20">
            <table class="min-w-full">
                <thead class="border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">#</th>
                        @foreach ($columns as $col)
                            <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">
                                {{ e($col['label']) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @foreach ($rows as $row)
                        @php $rank = $startRank + $loop->index; @endphp
                        <tr class="hover:bg-violet-500/5 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-zinc-500">{{ $rank }}
                            </td>
                            @foreach ($columns as $col)
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-300">
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
        <div class="py-12 text-center border border-zinc-800/60">
            <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('ranking.no_data') }}</p>
        </div>
    @endif
</div>
