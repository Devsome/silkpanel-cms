@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <h1 class="text-3xl font-bold text-white uppercase tracking-widest mb-8">
                {{ __('navigation.downloads') }}
            </h1>

            @if ($downloads->count() === 0)
                <div class="text-center py-20">
                    <svg class="mx-auto h-12 w-12 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                    </svg>
                    <p class="mt-4 text-gray-500">{{ __('downloads.no_downloads') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($downloads as $download)
                        <div
                            class="group rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur overflow-hidden hover:-translate-y-0.5 hover:shadow-2xl transition duration-300">
                            @if ($download->image)
                                <div class="aspect-video overflow-hidden bg-black/20">
                                    <img src="{{ asset('storage/' . $download->image) }}" alt="{{ e($download->name) }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                        loading="lazy">
                                </div>
                            @else
                                <div
                                    class="aspect-video flex items-center justify-center bg-linear-to-br from-emerald-900/30 to-cyan-900/20">
                                    <svg class="w-12 h-12 text-emerald-700" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-5">
                                <h2 class="text-lg font-bold text-white mb-2">{{ e($download->name) }}</h2>
                                @if ($download->description)
                                    <p class="text-sm text-gray-400 mb-4">{{ e($download->description) }}</p>
                                @endif
                                <a href="{{ e($download->link) }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 hover:brightness-110 transition">
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
            @endif
        </div>
    </section>
@endsection
