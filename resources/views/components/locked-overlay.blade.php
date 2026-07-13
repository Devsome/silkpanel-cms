@props([
    'locked' => false,
    'title' => null,
    'description' => null,
    'icon' => 'heroicon-o-lock-closed',
])

@unless ($locked)
    {{ $slot }}
@else
    <div class="sp-locked" role="group" aria-disabled="true">
        <div class="sp-locked__content" aria-hidden="true" inert>
            {{ $slot }}
        </div>

        <div class="sp-locked__overlay">
            <div class="sp-locked__card">
                <span class="sp-locked__badge">
                    <x-filament::icon :icon="$icon" class="sp-locked__icon" />
                </span>

                @if ($title)
                    <h3 class="sp-locked__title">{{ $title }}</h3>
                @endif

                @if ($description)
                    <p class="sp-locked__description">{{ $description }}</p>
                @endif

                @isset($actions)
                    <div class="sp-locked__actions">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endunless
