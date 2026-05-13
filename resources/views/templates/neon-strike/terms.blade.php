@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="bg-zinc-900 border border-violet-500/20 p-8">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-2">
                    {{ __('terms.section_label') }}</p>
                <h1 class="text-2xl font-black uppercase tracking-widest text-white mb-4">{{ __('terms.title') }}</h1>
                <div class="h-px bg-linear-to-r from-violet-500/40 to-transparent mb-6"></div>
                <div
                    class="prose prose-invert prose-sm max-w-none
                    prose-headings:font-bold prose-headings:uppercase prose-headings:tracking-widest prose-headings:text-white
                    prose-p:text-zinc-400 prose-a:text-violet-400 prose-strong:text-zinc-200
                    prose-hr:border-zinc-800">
                    {!! \App\Helpers\SettingHelper::get('tos_content', '') !!}
                </div>
            </div>
        </div>
    </section>
@endsection
