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
                    {{ __('tickets.section_label') }}</p>
                <h1 class="text-2xl font-black uppercase tracking-widest text-white">{{ __('tickets.title') }}</h1>
                <div class="mt-3 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            </div>

            <div class="flex justify-end mb-4">
                <a href="{{ route('tickets.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                    + {{ __('tickets.create_ticket') }}
                </a>
            </div>

            @if ($tickets->isEmpty())
                <div class="bg-zinc-900 border border-zinc-800 p-12 text-center">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('tickets.no_tickets') }}</p>
                </div>
            @else
                <div class="bg-zinc-900 border border-violet-500/20 divide-y divide-zinc-800/80">
                    @foreach ($tickets as $ticket)
                        @php
                            $statusColor = match ($ticket->status->value) {
                                'open' => 'text-violet-400 border-violet-500/40 bg-violet-500/10',
                                'in_progress' => 'text-cyan-400 border-cyan-500/40 bg-cyan-500/10',
                                'reopened' => 'text-sky-400 border-sky-500/40 bg-sky-500/10',
                                'closed' => 'text-zinc-500 border-zinc-600/40 bg-zinc-600/10',
                                default => 'text-zinc-400 border-zinc-600/40 bg-zinc-600/10',
                            };
                        @endphp
                        <div class="p-4 flex items-center justify-between gap-4 hover:bg-violet-500/5 transition">
                            <div class="flex items-center gap-4 min-w-0">
                                <div>
                                    <a href="{{ route('tickets.show', $ticket->id) }}"
                                        class="font-medium text-zinc-200 hover:text-violet-300 transition uppercase tracking-wide text-sm">
                                        {{ e($ticket->title) }}
                                    </a>
                                    <p class="text-xs font-mono text-zinc-600 mt-0.5">
                                        {{ $ticket->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span
                                    class="px-2 py-0.5 text-xs font-mono uppercase tracking-wider border {{ $statusColor }}">
                                    {{ $ticket->status->getLabel() }}
                                </span>
                                <a href="{{ route('tickets.show', $ticket->id) }}"
                                    class="text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                                    →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($tickets->hasPages())
                    <div class="mt-6">{{ $tickets->links() }}</div>
                @endif
            @endif
        </div>
    </section>
@endsection
