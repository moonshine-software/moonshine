<button
    {{ $attributes->class(['btn btn-primary btn-lg'])
        ->merge(['type' => 'button']) }}
>
    {{ $slot }}
</button>
