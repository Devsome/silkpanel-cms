@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-[1600px] px-4 md:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold font-headline gp-text-primary uppercase tracking-widest">
                    {{ __('navigation.history') }}
                </h1>
            </div>

            <livewire:histories.global-history />
        </div>
    </div>
@endsection
