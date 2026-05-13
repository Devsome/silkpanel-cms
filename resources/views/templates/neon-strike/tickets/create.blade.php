@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('tickets.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('tickets.back') }}
                </a>
            </div>

            <div class="bg-zinc-900 border border-violet-500/20 p-8">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-2">
                    {{ __('tickets.new_ticket') }}</p>
                <h1 class="text-xl font-black uppercase tracking-widest text-white mb-4">{{ __('tickets.create_ticket') }}
                </h1>
                <div class="h-px bg-linear-to-r from-violet-500/40 to-transparent mb-6"></div>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="title"
                            class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('tickets.form.subject') }}</label>
                        <input id="title" type="text" name="title" value="{{ old('title') }}" required
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition placeholder-zinc-600">
                        @error('subject')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id"
                            class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('tickets.form.category') }}</label>
                        <select id="category_id" name="category_id"
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition">
                            <option value="">{{ __('tickets.form.select_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ e($category->name) }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {{-- Priority (hidden, default medium) --}}
                        <input type="hidden" name="priority" value="{{ old('priority', 'medium') }}">

                        <label for="text"
                            class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('tickets.form.message') }}</label>
                        <textarea id="text" name="text" rows="6" required
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition placeholder-zinc-600 resize-none">{{ old('text') }}</textarea>
                        @error('text')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('tickets.form.attachments') }}</label>
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
                        @error('attachments')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                        @error('attachments.*')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="px-6 py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                            {{ __('tickets.form.submit') }}
                        </button>
                        <a href="{{ route('tickets.index') }}"
                            class="px-6 py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-zinc-500 border border-zinc-700 hover:text-zinc-300 hover:border-zinc-500 transition">
                            {{ __('tickets.form.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
