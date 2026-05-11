@php
    $refObj = $refObjs[$record->ref_item_id] ?? null;
    $icon = \App\Helpers\WebmallItemIconHelper::resolveIcon($refObj?->AssocFileIcon128);
    $isSeal = $refObj && \App\Helpers\WebmallItemIconHelper::isSeal($refObj);
@endphp

<div class="relative inline-flex">
    @if ($isSeal)
        <img class="pointer-events-none absolute inset-0 size-8" src="{{ asset('images/silkroad/item/seal.gif') }}" />
    @endif
    <img src="{{ asset('images/silkroad/' . $icon) }}" alt="{{ $record->item_name_snapshot ?? 'Item' }}"
        class="size-8 rounded border border-gray-300 bg-gray-100 object-contain dark:border-gray-600 dark:bg-gray-800">
</div>
