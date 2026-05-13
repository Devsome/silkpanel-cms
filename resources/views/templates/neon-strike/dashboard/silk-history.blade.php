@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('dashboard.back_to_dashboard') }}
                </a>
            </div>

            <div class="mb-6">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('dashboard.silk_history') }}</p>
                <h1 class="text-2xl font-black uppercase tracking-widest text-white">
                    {{ __('dashboard.silk_history_title') ?? __('dashboard.silk_history') }}</h1>
                <div class="mt-3 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            </div>

            @if (empty($history) || count($history) === 0)
                <div class="bg-zinc-900 border border-zinc-800 p-12 text-center">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">
                        {{ __('dashboard.no_silk_history') }}</p>
                </div>
            @else
                <div class="bg-zinc-900 border border-violet-500/20">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-800">
                                <th class="text-left px-4 py-3 text-xs font-mono uppercase tracking-wider text-zinc-500">
                                    {{ __('dashboard.history_date') }}</th>
                                <th class="text-left px-4 py-3 text-xs font-mono uppercase tracking-wider text-zinc-500">
                                    {{ __('dashboard.history_type') }}</th>
                                <th class="text-right px-4 py-3 text-xs font-mono uppercase tracking-wider text-zinc-500">
                                    {{ __('dashboard.history_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/60">
                            @foreach ($history as $entry)
                                <tr class="hover:bg-violet-500/5 transition">
                                    <td class="px-4 py-3 font-mono text-xs text-zinc-500">
                                        {{ isset($entry->created_at) ? $entry->created_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-zinc-400 text-xs uppercase tracking-wide">
                                        {{ e($entry->type ?? ($entry->description ?? '-')) }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right font-bold font-mono {{ ($entry->amount ?? 0) >= 0 ? 'text-violet-400' : 'text-red-400' }}">
                                        {{ ($entry->amount ?? 0) >= 0 ? '+' : '' }}{{ number_format($entry->amount ?? 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
@endsection
