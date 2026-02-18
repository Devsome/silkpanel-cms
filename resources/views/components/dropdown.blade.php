@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => '',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
        'top' => 'origin-top',
        default => 'ltr:origin-top-right rtl:origin-top-left end-0',
    };

    $width = match ($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        default => $width,
    };
@endphp

<div class="relative inline-block" x-data="{ open: false }" @click.away="open = false" @keydown.escape.stop="open = false">
    <div @click.stop="open = !open" @keydown.space.enter.prevent="open = !open" role="button" tabindex="0">
        {{ $trigger }}
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute mt-2 {{ $width }} rounded-lg shadow-xl {{ $alignmentClasses }}
        divide-y divide-gray-100 rounded-md bg-white shadow-lg outline-1 outline-black/5
        data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out
        data-leave:duration-75 data-leave:ease-in
        dark:divide-white/10 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
        style="display: none;" @click.stop="open = false">
        {{ $content }}
    </div>
</div>
