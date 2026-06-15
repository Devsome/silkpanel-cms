@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <a href="{{ route('dashboard') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            @if (session('success'))
                <div class="mb-6 ag-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 ag-card-glow p-6 md:p-8">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="ag-section-eyebrow">{{ __('ticket::ticket.page_index_subtitle') }}</p>
                        <h1 class="ag-section-title mt-2">{{ __('ticket::ticket.page_index_title') }}</h1>
                    </div>
                    <a href="{{ route('tickets.create') }}"
                        class="ag-btn-primary inline-flex items-center gap-2 rounded px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-wider">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('ticket::ticket.button_new_ticket') }}
                    </a>
                </div>
            </div>

            @if ($tickets->count() > 0)
                <div class="overflow-hidden ag-card">
                    <div class="overflow-x-auto">
                        <table class="ag-table min-w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('ticket::ticket.table_title') }}</th>
                                    <th>{{ __('ticket::ticket.table_category') }}</th>
                                    <th>{{ __('ticket::ticket.table_priority') }}</th>
                                    <th>{{ __('ticket::ticket.table_status') }}</th>
                                    <th>{{ __('ticket::ticket.table_updated') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr class="cursor-pointer transition hover:bg-white/5"
                                        onclick="window.location='{{ route('tickets.show', $ticket->id) }}'">
                                        <td class="ag-text-muted">#{{ $ticket->id }}</td>
                                        <td class="max-w-xs truncate font-medium ag-text-surface">
                                            <a href="{{ route('tickets.show', $ticket->id) }}"
                                                class="hover:ag-text-primary">
                                                {{ e($ticket->title) }}
                                            </a>
                                        </td>
                                        <td class="ag-text-muted">{{ $ticket->category?->name ?? '—' }}</td>
                                        <td>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs ag-font-display uppercase tracking-wide
                                                {{ $ticket->priority->value === 'urgent' ? 'bg-red-900/30 text-red-300' : ($ticket->priority->value === 'high' ? 'bg-orange-900/30 text-orange-300' : ($ticket->priority->value === 'medium' ? 'bg-blue-900/30 text-blue-300' : 'bg-zinc-800 text-zinc-300')) }}">
                                                {{ $ticket->priority->getLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs ag-font-display uppercase tracking-wide
                                                {{ $ticket->status->value === 'closed' ? 'bg-green-900/30 text-green-300' : ($ticket->status->value === 'in_progress' ? 'bg-cyan-900/30 text-cyan-300' : ($ticket->status->value === 'reopened' ? 'bg-sky-900/30 text-sky-300' : 'bg-zinc-800 text-zinc-300')) }}">
                                                {{ $ticket->status->getLabel() }}
                                            </span>
                                        </td>
                                        <td class="ag-text-muted">{{ $ticket->updated_at->diffForHumans() }}</td>
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
                <div class="ag-card p-10 text-center">
                    <p class="text-sm ag-text-muted">{{ __('ticket::ticket.page_index_empty') }}</p>
                    <a href="{{ route('tickets.create') }}"
                        class="ag-btn-primary mt-4 inline-flex items-center rounded px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-wider">
                        {{ __('ticket::ticket.button_new_ticket') }}
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
