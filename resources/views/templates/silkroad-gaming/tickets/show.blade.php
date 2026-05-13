@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8 space-y-6">

            <a href="{{ route('tickets.index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ticket::ticket.back_to_tickets') }}
            </a>

            @if (session('success'))
                <div class="rounded-xl border border-emerald-800/40 bg-emerald-900/20 p-4">
                    <p class="text-sm text-emerald-300">{{ session('success') }}</p>
                </div>
            @endif

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                            {{ __('ticket::ticket.table_view_ticket', ['id' => $ticket->id]) }}
                        </p>
                        <h1 class="mt-2 text-2xl font-bold text-white">{{ e($ticket->title) }}</h1>
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('ticket::ticket.table_category') }}: {{ $ticket->category?->name ?? '—' }}
                            &middot;
                            {{ __('ticket::ticket.table_created') }}: {{ $ticket->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide
                            {{ $ticket->priority->value === 'urgent' ? 'bg-red-900/30 text-red-400' : ($ticket->priority->value === 'high' ? 'bg-orange-900/30 text-orange-400' : ($ticket->priority->value === 'medium' ? 'bg-blue-900/30 text-blue-400' : 'bg-gray-800 text-gray-400')) }}">
                            {{ $ticket->priority->getLabel() }}
                        </span>
                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide
                            {{ $ticket->status->value === 'closed' ? 'bg-emerald-900/30 text-emerald-400' : ($ticket->status->value === 'in_progress' ? 'bg-yellow-900/30 text-yellow-400' : ($ticket->status->value === 'reopened' ? 'bg-sky-900/30 text-sky-400' : 'bg-gray-800 text-gray-400')) }}">
                            {{ $ticket->status->getLabel() }}
                        </span>

                        @if ($ticket->isOpen())
                            <form method="POST" action="{{ route('tickets.close', $ticket->id) }}" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('{{ __('ticket::ticket.confirm_close') }}')"
                                    class="cursor-pointer rounded-lg border border-red-700/40 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-red-400 hover:bg-red-900/20 transition">
                                    {{ __('ticket::ticket.button_close_ticket') }}
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('tickets.reopen', $ticket->id) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="cursor-pointer rounded-lg border border-emerald-700/40 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-emerald-400 hover:bg-emerald-900/20 transition">
                                    {{ __('ticket::ticket.button_reopen_ticket') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    {{-- Initial message --}}
                    <div class="flex justify-start">
                        <div
                            class="max-w-3xl rounded-2xl rounded-tl-sm border border-gray-800 bg-gray-900/80 px-4 py-3 text-sm text-gray-100">
                            <p class="whitespace-pre-wrap leading-relaxed">{{ e($ticket->text) }}</p>
                            @if ($ticket->attachments->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($ticket->attachments as $attachment)
                                        <a href="{{ $attachment->getPublicUrl() }}" target="_blank"
                                            rel="noopener noreferrer">
                                            <img src="{{ $attachment->getPublicUrl() }}"
                                                alt="{{ e($attachment->original_name) }}"
                                                class="h-20 w-auto rounded-lg object-cover hover:opacity-80 transition">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-2 text-xs text-gray-500">
                                {{ $ticket->user->name ?? Auth::user()->name }} &middot;
                                {{ $ticket->created_at?->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    @foreach ($ticket->replies as $reply)
                        <div class="flex {{ $reply->is_admin_reply ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="max-w-3xl rounded-2xl px-4 py-3 text-sm
                                {{ $reply->is_admin_reply
                                    ? 'rounded-tr-sm border border-emerald-800/40 bg-emerald-900/20 text-emerald-100'
                                    : 'rounded-tl-sm border border-gray-800 bg-gray-900/80 text-gray-100' }}">
                                @if ($reply->is_admin_reply)
                                    <p class="mb-1 text-xs font-bold uppercase tracking-wider text-emerald-400">
                                        {{ __('ticket::ticket.label_admin') }}
                                    </p>
                                @endif
                                <p class="whitespace-pre-wrap leading-relaxed">{{ e($reply->text) }}</p>
                                @if ($reply->attachments->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach ($reply->attachments as $attachment)
                                            <a href="{{ $attachment->getPublicUrl() }}" target="_blank"
                                                rel="noopener noreferrer">
                                                <img src="{{ $attachment->getPublicUrl() }}"
                                                    alt="{{ e($attachment->original_name) }}"
                                                    class="h-20 w-auto rounded-lg object-cover hover:opacity-80 transition">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <div
                                    class="mt-2 text-xs {{ $reply->is_admin_reply ? 'text-emerald-500/70' : 'text-gray-500' }}">
                                    {{ $reply->created_at?->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($ticket->isOpen())
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-400/70 mb-4">
                        {{ __('ticket::ticket.section_add_reply') }}
                    </h2>
                    <form method="POST" action="{{ route('tickets.reply', $ticket->id) }}" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <div>
                            <textarea name="text" rows="4" required placeholder="{{ __('ticket::ticket.field_reply_placeholder') }}"
                                class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm @error('text') border-red-500 @enderror">{{ old('text') }}</textarea>
                            @error('text')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">
                                {{ __('ticket::ticket.field_attachments') }}
                                <span
                                    class="text-xs text-gray-500">({{ __('ticket::ticket.field_attachments_hint') }})</span>
                            </label>
                            <input type="file" name="attachments[]" multiple
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                class="block w-full text-sm text-gray-400 file:mr-4 file:rounded-lg file:border-0 file:px-4 file:py-2 file:text-xs file:font-bold file:uppercase file:tracking-wider file:bg-linear-to-r file:from-emerald-500 file:to-cyan-500 file:text-gray-950 file:cursor-pointer hover:file:brightness-110">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="cursor-pointer rounded-lg bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 px-5 py-2 text-xs font-bold uppercase tracking-wider hover:brightness-110 transition">
                                {{ __('ticket::ticket.button_send_reply') }}
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-4 text-center text-sm text-gray-500">
                    {{ __('ticket::ticket.ticket_closed_notice') }}
                </div>
            @endif

        </div>
    </section>
@endsection
