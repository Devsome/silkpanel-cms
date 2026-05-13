@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('tickets.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('tickets.back') }}
                </a>
            </div>

            {{-- Ticket header --}}
            <div class="bg-zinc-900 border border-violet-500/20 p-6 mb-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-1">#{{ $ticket->id }}
                        </p>
                        <h1 class="text-lg font-black uppercase tracking-wider text-white">{{ e($ticket->title) }}</h1>
                    </div>
                    @php
                        $statusColor = match ($ticket->status->value) {
                            'open' => 'text-violet-400 border-violet-500/40 bg-violet-500/10',
                            'in_progress' => 'text-cyan-400 border-cyan-500/40 bg-cyan-500/10',
                            'reopened' => 'text-sky-400 border-sky-500/40 bg-sky-500/10',
                            'closed' => 'text-zinc-500 border-zinc-600/40 bg-zinc-600/10',
                            default => 'text-zinc-400 border-zinc-600/40 bg-zinc-600/10',
                        };
                    @endphp
                    <span
                        class="px-2 py-1 text-xs font-mono uppercase tracking-wider border flex-shrink-0 {{ $statusColor }}">
                        {{ $ticket->status->getLabel() }}
                    </span>
                </div>
            </div>

            {{-- Messages --}}
            <div class="space-y-3 mb-4">
                @foreach ($ticket->replies as $reply)
                    @php $isStaff = $reply->is_admin_reply ?? false; @endphp
                    <div
                        class="bg-zinc-900 border {{ $isStaff ? 'border-fuchsia-500/25 bg-fuchsia-500/5' : 'border-violet-500/15' }} p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span
                                    class="flex h-6 w-6 items-center justify-center {{ $isStaff ? 'bg-fuchsia-500/20 text-fuchsia-400' : 'bg-violet-500/20 text-violet-400' }} text-xs font-bold font-mono">
                                    {{ strtoupper(substr($reply->user?->name ?? ($reply->is_admin_reply ? 'S' : 'U'), 0, 1)) }}
                                </span>
                                <span
                                    class="text-xs font-mono uppercase tracking-wider {{ $isStaff ? 'text-fuchsia-400' : 'text-zinc-400' }}">
                                    {{ $isStaff ? __('tickets.staff') : e($reply->user?->name ?? __('tickets.you')) }}
                                </span>
                            </div>
                            <span
                                class="text-xs font-mono text-zinc-600">{{ $reply->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="text-sm text-zinc-400 leading-relaxed whitespace-pre-wrap">{{ e($reply->text) }}
                        </div>
                        @if ($reply->attachments->count())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($reply->attachments as $att)
                                    <a href="{{ Storage::url($att->file_path) }}" target="_blank" rel="noopener noreferrer"
                                        class="block border border-zinc-700 hover:border-violet-500/40 overflow-hidden transition">
                                        <img src="{{ Storage::url($att->file_path) }}" alt="{{ e($att->original_name) }}"
                                            class="h-20 w-auto object-cover">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Reply form (if not closed) --}}
            @if ($ticket->status->value !== 'closed')
                <div class="bg-zinc-900 border border-violet-500/20 p-6 mb-4">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-4">
                        {{ __('tickets.reply') }}</p>
                    <form method="POST" action="{{ route('tickets.reply', $ticket->id) }}" enctype="multipart/form-data"
                        class="space-y-3">
                        @csrf
                        <textarea name="text" rows="4" required
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition placeholder-zinc-600 resize-none"
                            placeholder="{{ __('tickets.form.message') }}"></textarea>
                        @error('text')
                            <p class="text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                        <label
                            class="flex items-center gap-3 cursor-pointer border border-dashed border-zinc-700 hover:border-violet-500/50 bg-zinc-950 px-4 py-3 transition">
                            <svg class="w-4 h-4 text-zinc-600 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span class="text-xs font-mono text-zinc-600">{{ __('tickets.form.attachments_hint') }}</span>
                            <input type="file" name="attachments[]" multiple
                                accept="image/jpeg,image/png,image/gif,image/webp" class="sr-only">
                        </label>
                        @error('attachments.*')
                            <p class="text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                        <button type="submit"
                            class="px-6 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                            {{ __('tickets.send_reply') }}
                        </button>
                    </form>
                </div>

                <form method="POST" action="{{ route('tickets.close', $ticket->id) }}">
                    @csrf
                    <button type="submit"
                        class="text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-red-400 transition border border-transparent hover:border-red-500/30 px-3 py-1.5">
                        {{ __('tickets.close_ticket') }}
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('tickets.reopen', $ticket->id) }}">
                    @csrf
                    <button type="submit"
                        class="text-xs font-mono uppercase tracking-wider text-violet-500 hover:text-violet-300 border border-violet-500/30 px-4 py-2 hover:bg-violet-500/10 transition">
                        {{ __('tickets.reopen_ticket') }}
                    </button>
                </form>
            @endif
        </div>
    </section>
@endsection
