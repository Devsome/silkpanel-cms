@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <div class="mb-8 ag-card-glow p-6 md:p-8">
                <p class="ag-section-eyebrow">{{ __('downloads.title') }}</p>
                <h1 class="ag-section-title mt-2">{{ __('downloads.title') }}</h1>
            </div>

            @if ($downloads->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($downloads as $download)
                        <div class="group ag-card overflow-hidden transition-all duration-300 hover:-translate-y-0.5 hover:shadow-2xl">
                            @if ($download->image)
                                <div class="aspect-video overflow-hidden bg-black/20">
                                    <img src="{{ asset('storage/' . $download->image) }}" alt="{{ e($download->name) }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                        loading="lazy">
                                </div>
                            @else
                                <div class="aspect-video flex items-center justify-center"
                                    style="background:rgba(13,18,36,0.8);">
                                    <svg class="h-12 w-12 ag-text-muted" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                                    </svg>
                                </div>
                            @endif

                            <div class="p-5">
                                <h2 class="text-lg ag-font-display font-bold uppercase tracking-wide ag-text-surface">
                                    {{ e($download->name) }}
                                </h2>

                                @if ($download->description)
                                    <p class="mt-3 text-sm leading-relaxed ag-text-muted">
                                        {{ e($download->description) }}
                                    </p>
                                @endif

                                <a href="{{ e($download->link) }}" target="_blank" rel="noopener noreferrer"
                                    class="ag-btn-primary mt-5 inline-flex items-center gap-2 px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-widest transition-all hover:brightness-110">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ __('downloads.download') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="ag-card p-10 text-center">
                    <p class="ag-text-muted">{{ __('downloads.no_downloads') }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
