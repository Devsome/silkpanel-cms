@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8">
            <div class="ag-card p-6 md:p-10">
                <p class="mb-2 ag-section-eyebrow">Legal</p>
                <h1 class="ag-section-title">{{ __('terms.title') }}</h1>

                <div class="my-6 h-px"
                    style="background:linear-gradient(to right,rgba(34,211,238,0.7),rgba(34,211,238,0));"></div>

                <div class="ag-terms-content ag-text-surface leading-relaxed">
                    @if (!empty($tosText))
                        {!! $tosText !!}
                    @else
                        <p class="ag-text-muted">{{ __('terms.title') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <style>
        .ag-terms-content h1, .ag-terms-content h2, .ag-terms-content h3 {
            font-family: 'Chakra Petch', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--ag-primary);
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .ag-terms-content h1 { font-size: 1.875rem; }
        .ag-terms-content h2 { font-size: 1.5rem; }
        .ag-terms-content h3 { font-size: 1.25rem; }
        .ag-terms-content p { margin-bottom: 1.25rem; color: rgba(200,220,255,0.7); }
        .ag-terms-content a { color: var(--ag-primary); text-decoration: underline; }
        .ag-terms-content ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; color: rgba(200,220,255,0.7); }
        .ag-terms-content ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 1.25rem; color: rgba(200,220,255,0.7); }
        .ag-terms-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.25rem; }
        .ag-terms-content th, .ag-terms-content td { border: 1px solid rgba(34,211,238,0.2); padding: 0.5rem; }
        .ag-terms-content th { background: rgba(34,211,238,0.05); color: var(--ag-primary); }
    </style>
@endsection
