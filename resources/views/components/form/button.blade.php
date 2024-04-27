<button
    {{ $attributes->class(['btn'])
        ->merge(['type' => 'button']) }}
>
    {{ $slot ?? '' }}
</button>
