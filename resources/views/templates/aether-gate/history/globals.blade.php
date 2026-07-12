@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <div class="mb-8">
                <p class="ag-section-eyebrow">{{ __('navigation.history') }}</p>
                <h1 class="ag-section-title mt-2">{{ __('history.global_title') }}</h1>
            </div>

            <livewire:histories.global-history />
        </div>
    </section>
@endsection
