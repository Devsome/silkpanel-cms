@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8">
            <div class="gp-card gp-ornate-border p-6 md:p-10">
                <p class="mb-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-outline">
                    Legal
                </p>
                <h1 class="text-3xl md:text-4xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('terms.title') }}
                </h1>

                <div class="my-6 h-px"
                    style="background: linear-gradient(to right, rgba(212,175,55,0.7), rgba(212,175,55,0));"></div>

                <div
                    class="gp-text-on-surface leading-relaxed [&_h1]:font-headline [&_h1]:uppercase [&_h1]:tracking-wide [&_h1]:text-3xl [&_h1]:gp-text-primary [&_h2]:font-headline [&_h2]:uppercase [&_h2]:tracking-wide [&_h2]:text-2xl [&_h2]:gp-text-primary [&_h3]:font-headline [&_h3]:uppercase [&_h3]:tracking-wide [&_h3]:text-xl [&_h3]:gp-text-primary [&_p]:mb-4 [&_a]:gp-text-primary [&_a]:underline [&_blockquote]:border-l-2 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:gp-text-on-surface-variant [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_table]:w-full [&_table]:border-collapse [&_th]:border [&_th]:p-2 [&_td]:border [&_td]:p-2">
                    @if (!empty($tosText))
                        {!! $tosText !!}
                    @else
                        <p class="gp-text-on-surface-variant">{{ __('terms.title') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
