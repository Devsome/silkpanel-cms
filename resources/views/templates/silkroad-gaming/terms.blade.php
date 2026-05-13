@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 md:px-8">
            <h1 class="text-3xl font-bold text-white uppercase tracking-widest mb-8">
                {{ __('terms.title') }}
            </h1>

            <div
                class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8 prose prose-invert prose-emerald max-w-none">
                {!! $content !!}
            </div>
        </div>
    </section>
@endsection
