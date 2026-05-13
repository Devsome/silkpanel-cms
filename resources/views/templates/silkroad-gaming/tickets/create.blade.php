@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 md:px-8">

            <a href="{{ route('tickets.index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition mb-6">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ticket::ticket.back_to_tickets') }}
            </a>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8">
                <h1 class="text-3xl font-bold text-white uppercase tracking-widest mb-2">
                    {{ __('ticket::ticket.page_create_title') }}
                </h1>
                <p class="text-sm text-gray-400 mb-6">{{ __('ticket::ticket.page_create_subtitle') }}</p>

                @php $inputClass = 'w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm'; @endphp

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ __('ticket::ticket.field_title') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            class="{{ $inputClass }} @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ __('ticket::ticket.field_category') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                            class="{{ $inputClass }} @error('category_id') border-red-500 @enderror">
                            <option value="">{{ __('ticket::ticket.select_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ e($category->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ __('ticket::ticket.field_priority') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="priority" name="priority" required
                            class="{{ $inputClass }} @error('priority') border-red-500 @enderror">
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->value }}"
                                    {{ old('priority', 'medium') === $priority->value ? 'selected' : '' }}>
                                    {{ $priority->getLabel() }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="text" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ __('ticket::ticket.field_text') }} <span class="text-red-400">*</span>
                        </label>
                        <textarea id="text" name="text" rows="6" required
                            class="{{ $inputClass }} @error('text') border-red-500 @enderror">{{ old('text') }}</textarea>
                        @error('text')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="attachments" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ __('ticket::ticket.field_attachments') }}
                            <span class="text-xs text-gray-500">({{ __('ticket::ticket.field_attachments_hint') }})</span>
                        </label>
                        <input type="file" id="attachments" name="attachments[]" multiple
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            class="block w-full text-sm text-gray-400 file:mr-4 file:rounded-lg file:border-0 file:px-4 file:py-2 file:text-xs file:font-bold file:uppercase file:tracking-wider file:bg-linear-to-r file:from-emerald-500 file:to-cyan-500 file:text-gray-950 file:cursor-pointer hover:file:brightness-110">
                        @error('attachments.*')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('tickets.index') }}"
                            class="px-4 py-2 rounded-lg border border-gray-700 text-gray-400 text-xs font-semibold hover:text-white hover:border-gray-600 transition">
                            {{ __('ticket::ticket.button_cancel') }}
                        </a>
                        <button type="submit"
                            class="cursor-pointer rounded-lg bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 px-5 py-2 text-xs font-bold uppercase tracking-wider hover:brightness-110 transition">
                            {{ __('ticket::ticket.button_submit_ticket') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
