@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition mb-6">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-800/40 bg-emerald-900/20 p-4">
                    <p class="text-sm text-emerald-300">{{ session('success') }}</p>
                </div>
            @endif

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8 mb-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-3xl font-bold text-white uppercase tracking-widest">
                            {{ __('ticket::ticket.page_index_title') }}
                        </h1>
                        <p class="mt-2 text-sm text-gray-400">{{ __('ticket::ticket.page_index_subtitle') }}</p>
                    </div>
                    <a href="{{ route('tickets.create') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 px-4 py-2 text-xs font-bold uppercase tracking-wider hover:brightness-110 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('ticket::ticket.button_new_ticket') }}
                    </a>
                </div>
            </div>

            @if ($tickets->count() > 0)
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800">
                            <thead class="bg-gray-900/80">
                                <tr>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        #</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        {{ __('ticket::ticket.table_title') }}</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        {{ __('ticket::ticket.table_category') }}</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        {{ __('ticket::ticket.table_priority') }}</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        {{ __('ticket::ticket.table_status') }}</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        {{ __('ticket::ticket.table_updated') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800/50">
                                @foreach ($tickets as $ticket)
                                    <tr class="cursor-pointer hover:bg-emerald-500/5 transition"
                                        onclick="window.location='{{ route('tickets.show', $ticket->id) }}'">
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">#{{ $ticket->id }}
                                        </td>
                                        <td class="max-w-xs truncate px-5 py-4 text-sm font-medium text-gray-100">
                                            <a href="{{ route('tickets.show', $ticket->id) }}"
                                                class="hover:text-emerald-400 transition">
                                                {{ e($ticket->title) }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-400">
                                            {{ $ticket->category?->name ?? '—' }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span
                                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide
                                                {{ $ticket->priority->value === 'urgent' ? 'bg-red-900/30 text-red-400' : ($ticket->priority->value === 'high' ? 'bg-orange-900/30 text-orange-400' : ($ticket->priority->value === 'medium' ? 'bg-blue-900/30 text-blue-400' : 'bg-gray-800 text-gray-400')) }}">
                                                {{ $ticket->priority->getLabel() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span
                                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide
                                                {{ $ticket->status->value === 'closed' ? 'bg-emerald-900/30 text-emerald-400' : ($ticket->status->value === 'in_progress' ? 'bg-yellow-900/30 text-yellow-400' : ($ticket->status->value === 'reopened' ? 'bg-sky-900/30 text-sky-400' : 'bg-gray-800 text-gray-400')) }}">
                                                {{ $ticket->status->getLabel() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
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
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-10 text-center">
                    <p class="text-sm text-gray-500 mb-4">{{ __('ticket::ticket.page_index_empty') }}</p>
                    <a href="{{ route('tickets.create') }}"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 px-4 py-2 text-xs font-bold uppercase tracking-wider hover:brightness-110 transition">
                        {{ __('ticket::ticket.button_new_ticket') }}
                    </a>
                </div>
            @endif

        </div>
    </section>
@endsection
