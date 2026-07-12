@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-white uppercase tracking-widest">
                    {{ __('navigation.history') }}
                </h1>
            </div>

            <livewire:histories.global-history />
        </div>
    </div>
@endsection
