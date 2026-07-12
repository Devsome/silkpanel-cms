@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 border-b border-zinc-800 pb-4">
                <h1 class="text-sm font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                    {{ __('navigation.history') }}
                </h1>
            </div>

            <livewire:histories.unique-history />
        </div>
    </section>
@endsection
