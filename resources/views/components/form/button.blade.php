<button
    {{ $attributes->class(['btn btn-primary'])
        ->merge(['type' => 'button']) }}
>
    {{ $slot }}
</button>
