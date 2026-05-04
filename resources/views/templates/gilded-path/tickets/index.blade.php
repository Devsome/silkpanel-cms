@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <a href="{{ route('dashboard') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-on-surface-variant transition-colors hover:gp-text-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            @if (session('success'))
                <div class="mb-6 gp-card p-4" style="border:1px solid rgba(120,220,140,0.4);">
                    <p class="text-sm text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            <div class="mb-6 gp-card gp-ornate-border p-6 md:p-8">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                            {{ __('ticket::ticket.page_index_title') }}
                        </h1>
                        <p class="mt-2 text-sm gp-text-on-surface-variant">
                            {{ __('ticket::ticket.page_index_subtitle') }}
                        </p>
                    </div>
                    <a href="{{ route('tickets.create') }}"
                        class="gp-gold-btn inline-flex items-center gap-2 rounded px-4 py-2 text-xs font-headline font-bold uppercase tracking-wider">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('ticket::ticket.button_new_ticket') }}
                    </a>
                </div>
            </div>

            @if ($tickets->count() > 0)
                <div class="overflow-hidden rounded gp-card gp-ornate-border">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y" style="border-color:rgba(242,202,80,0.15);">
                            <thead class="gp-card-lowest">
                                <tr>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-headline uppercase tracking-widest gp-text-outline">
                                        #</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-headline uppercase tracking-widest gp-text-outline">
                                        {{ __('ticket::ticket.table_title') }}</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-headline uppercase tracking-widest gp-text-outline">
                                        {{ __('ticket::ticket.table_category') }}</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-headline uppercase tracking-widest gp-text-outline">
                                        {{ __('ticket::ticket.table_priority') }}</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-headline uppercase tracking-widest gp-text-outline">
                                        {{ __('ticket::ticket.table_status') }}</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-headline uppercase tracking-widest gp-text-outline">
                                        {{ __('ticket::ticket.table_updated') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color:rgba(242,202,80,0.1);">
                                @foreach ($tickets as $ticket)
                                    <tr class="cursor-pointer transition hover:bg-black/10"
                                        onclick="window.location='{{ route('tickets.show', $ticket->id) }}'">
                                        <td class="whitespace-nowrap px-5 py-4 text-sm gp-text-on-surface-variant">
                                            #{{ $ticket->id }}</td>
                                        <td class="max-w-xs truncate px-5 py-4 text-sm font-medium gp-text-on-surface">
                                            <a href="{{ route('tickets.show', $ticket->id) }}"
                                                class="hover:gp-text-primary">
                                                {{ e($ticket->title) }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm gp-text-on-surface-variant">
                                            {{ $ticket->category?->name ?? '—' }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span
                                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-headline uppercase tracking-wide
                                                {{ $ticket->priority->value === 'urgent' ? 'bg-red-900/30 text-red-300' : ($ticket->priority->value === 'high' ? 'bg-orange-900/30 text-orange-300' : ($ticket->priority->value === 'medium' ? 'bg-blue-900/30 text-blue-300' : 'bg-zinc-800 text-zinc-300')) }}">
                                                {{ $ticket->priority->getLabel() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span
                                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-headline uppercase tracking-wide
                                                {{ $ticket->status->value === 'closed' ? 'bg-green-900/30 text-green-300' : ($ticket->status->value === 'in_progress' ? 'bg-yellow-900/30 text-yellow-300' : ($ticket->status->value === 'reopened' ? 'bg-sky-900/30 text-sky-300' : 'bg-zinc-800 text-zinc-300')) }}">
                                                {{ $ticket->status->getLabel() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm gp-text-on-surface-variant">
                                            {{ $ticket->updated_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $tickets->links() }}
                </div>
            @else
                <div class="gp-card gp-ornate-border p-10 text-center">
                    <p class="text-sm gp-text-on-surface-variant">{{ __('ticket::ticket.page_index_empty') }}</p>
                    <a href="{{ route('tickets.create') }}"
                        class="mt-4 inline-flex items-center rounded gp-gold-btn px-4 py-2 text-xs font-headline font-bold uppercase tracking-wider">
                        {{ __('ticket::ticket.button_new_ticket') }}
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
