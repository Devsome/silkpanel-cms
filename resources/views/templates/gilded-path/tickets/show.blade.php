@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8 space-y-6">
            <a href="{{ route('tickets.index') }}"
                class="inline-flex items-center gap-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-on-surface-variant transition-colors hover:gp-text-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ticket::ticket.back_to_tickets') }}
            </a>

            @if (session('success'))
                <div class="gp-card p-4" style="border:1px solid rgba(120,220,140,0.4);">
                    <p class="text-sm text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            <div class="gp-card gp-ornate-border p-6 md:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline">
                            {{ __('ticket::ticket.table_view_ticket', ['id' => $ticket->id]) }}
                        </p>
                        <h1 class="mt-2 text-2xl font-headline font-black uppercase tracking-wide gp-text-primary">
                            {{ e($ticket->title) }}
                        </h1>
                        <p class="mt-2 text-xs gp-text-on-surface-variant">
                            {{ __('ticket::ticket.table_category') }}: {{ $ticket->category?->name ?? '—' }}
                            &middot;
                            {{ __('ticket::ticket.table_created') }}: {{ $ticket->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-headline uppercase tracking-wide
                            {{ $ticket->priority->value === 'urgent' ? 'bg-red-900/30 text-red-300' : ($ticket->priority->value === 'high' ? 'bg-orange-900/30 text-orange-300' : ($ticket->priority->value === 'medium' ? 'bg-blue-900/30 text-blue-300' : 'bg-zinc-800 text-zinc-300')) }}">
                            {{ $ticket->priority->getLabel() }}
                        </span>
                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-headline uppercase tracking-wide
                            {{ $ticket->status->value === 'closed' ? 'bg-green-900/30 text-green-300' : ($ticket->status->value === 'in_progress' ? 'bg-yellow-900/30 text-yellow-300' : ($ticket->status->value === 'reopened' ? 'bg-sky-900/30 text-sky-300' : 'bg-zinc-800 text-zinc-300')) }}">
                            {{ $ticket->status->getLabel() }}
                        </span>

                        @if ($ticket->isOpen())
                            <form method="POST" action="{{ route('tickets.close', $ticket->id) }}" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('{{ __('ticket::ticket.confirm_close') }}')"
                                    class="cursor-pointer rounded border border-red-500/40 px-3 py-1.5 text-xs font-headline uppercase tracking-wide text-red-300 transition hover:bg-red-900/20">
                                    {{ __('ticket::ticket.button_close_ticket') }}
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('tickets.reopen', $ticket->id) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="cursor-pointer rounded border border-green-500/40 px-3 py-1.5 text-xs font-headline uppercase tracking-wide text-green-300 transition hover:bg-green-900/20">
                                    {{ __('ticket::ticket.button_reopen_ticket') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex justify-start">
                        <div class="max-w-3xl rounded gp-card-lowest px-4 py-3 text-sm gp-text-on-surface">
                            <p class="whitespace-pre-wrap leading-relaxed">{{ e($ticket->text) }}</p>
                            @if ($ticket->attachments->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($ticket->attachments as $attachment)
                                        <a href="{{ $attachment->getPublicUrl() }}" target="_blank"
                                            rel="noopener noreferrer">
                                            <img src="{{ $attachment->getPublicUrl() }}"
                                                alt="{{ e($attachment->original_name) }}"
                                                class="h-20 w-auto rounded object-cover transition hover:opacity-80">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-2 text-xs gp-text-on-surface-variant">
                                {{ $ticket->user->name ?? Auth::user()->name }} ·
                                {{ $ticket->created_at?->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    @foreach ($ticket->replies as $reply)
                        <div class="flex {{ $reply->is_admin_reply ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="max-w-3xl rounded px-4 py-3 text-sm {{ $reply->is_admin_reply ? 'bg-amber-900/20 text-amber-100' : 'gp-card-lowest gp-text-on-surface' }}">
                                @if ($reply->is_admin_reply)
                                    <p class="mb-1 text-xs font-headline uppercase tracking-wider text-amber-300">
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
                                                    class="h-20 w-auto rounded object-cover transition hover:opacity-80">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <div
                                    class="mt-2 text-xs {{ $reply->is_admin_reply ? 'text-amber-300/80' : 'gp-text-on-surface-variant' }}">
                                    {{ $reply->created_at?->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($ticket->isOpen())
                <div class="gp-card gp-ornate-border p-6 md:p-8">
                    <h2 class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline">
                        {{ __('ticket::ticket.section_add_reply') }}
                    </h2>
                    <form method="POST" action="{{ route('tickets.reply', $ticket->id) }}" enctype="multipart/form-data"
                        class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <textarea name="text" rows="4" required placeholder="{{ __('ticket::ticket.field_reply_placeholder') }}"
                                class="gp-input w-full rounded px-3 py-2 text-sm @error('text') border-red-500 @enderror">{{ old('text') }}</textarea>
                            @error('text')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-headline uppercase tracking-wider gp-text-outline">
                                {{ __('ticket::ticket.field_attachments') }}
                                <span
                                    class="normal-case gp-text-on-surface-variant">({{ __('ticket::ticket.field_attachments_hint') }})</span>
                            </label>
                            <input type="file" name="attachments[]" multiple
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                class="block w-full text-sm gp-text-on-surface-variant file:mr-4 file:rounded file:border-0 file:px-4 file:py-2 file:text-xs file:font-headline file:font-bold file:uppercase file:tracking-wider file:gp-gold-btn">
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="cursor-pointer rounded gp-gold-btn px-4 py-2 text-xs font-headline font-bold uppercase tracking-wider">
                                {{ __('ticket::ticket.button_send_reply') }}
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="gp-card p-4 text-center text-sm gp-text-on-surface-variant"
                    style="border:1px solid rgba(242,202,80,0.2);">
                    {{ __('ticket::ticket.ticket_closed_notice') }}
                </div>
            @endif
        </div>
    </section>
@endsection
