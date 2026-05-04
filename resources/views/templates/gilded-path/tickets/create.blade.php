@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 md:px-8">
            <a href="{{ route('tickets.index') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-on-surface-variant transition-colors hover:gp-text-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ticket::ticket.back_to_tickets') }}
            </a>

            <div class="gp-card gp-ornate-border p-6 md:p-8">
                <h1 class="text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('ticket::ticket.page_create_title') }}
                </h1>
                <p class="mt-2 text-sm gp-text-on-surface-variant">{{ __('ticket::ticket.page_create_subtitle') }}</p>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
                    class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="title"
                            class="mb-1 block text-xs font-headline uppercase tracking-wider gp-text-outline">
                            {{ __('ticket::ticket.field_title') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            class="gp-input w-full rounded px-3 py-2 text-sm @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id"
                            class="mb-1 block text-xs font-headline uppercase tracking-wider gp-text-outline">
                            {{ __('ticket::ticket.field_category') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                            class="gp-input w-full rounded px-3 py-2 text-sm @error('category_id') border-red-500 @enderror">
                            <option value="">{{ __('ticket::ticket.select_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ e($category->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority"
                            class="mb-1 block text-xs font-headline uppercase tracking-wider gp-text-outline">
                            {{ __('ticket::ticket.field_priority') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="priority" name="priority" required
                            class="gp-input w-full rounded px-3 py-2 text-sm @error('priority') border-red-500 @enderror">
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->value }}"
                                    {{ old('priority', 'medium') === $priority->value ? 'selected' : '' }}>
                                    {{ $priority->getLabel() }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="text"
                            class="mb-1 block text-xs font-headline uppercase tracking-wider gp-text-outline">
                            {{ __('ticket::ticket.field_text') }} <span class="text-red-400">*</span>
                        </label>
                        <textarea id="text" name="text" rows="6" required
                            class="gp-input w-full rounded px-3 py-2 text-sm @error('text') border-red-500 @enderror">{{ old('text') }}</textarea>
                        @error('text')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="attachments"
                            class="mb-1 block text-xs font-headline uppercase tracking-wider gp-text-outline">
                            {{ __('ticket::ticket.field_attachments') }}
                            <span
                                class="normal-case gp-text-on-surface-variant">({{ __('ticket::ticket.field_attachments_hint') }})</span>
                        </label>
                        <input type="file" id="attachments" name="attachments[]" multiple
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            class="block w-full text-sm gp-text-on-surface-variant file:mr-4 file:rounded file:border-0 file:px-4 file:py-2 file:text-xs file:font-headline file:font-bold file:uppercase file:tracking-wider file:gp-gold-btn">
                        @error('attachments.*')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('tickets.index') }}"
                            class="px-4 py-2 text-xs font-headline font-bold uppercase tracking-wider gp-text-on-surface-variant hover:gp-text-on-surface">
                            {{ __('ticket::ticket.button_cancel') }}
                        </a>
                        <button type="submit"
                            class="cursor-pointer rounded gp-gold-btn px-4 py-2 text-xs font-headline font-bold uppercase tracking-wider">
                            {{ __('ticket::ticket.button_submit_ticket') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
