@if ($errors->any())
    <div
        {{ $attributes->merge(['class' => 'rounded-md border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-950']) }}>
        <div class="font-medium text-red-800 dark:text-red-200">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-2 space-y-1 text-sm text-red-700 dark:text-red-300">
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
