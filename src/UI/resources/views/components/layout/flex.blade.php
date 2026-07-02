@props([
    'components' => [],
    'isWithoutSpace' => false,
    'itemsAlign' => 'center',
    'justifyAlign' => 'start',
])
<div
    {{ $attributes
        ->class([
            'flex',
            'gap-4' => !$isWithoutSpace,
            'items-' . $itemsAlign,
            'justify-' . $justifyAlign
        ])
    }}
>
    @foreach($components as $component)
        {!! $component !!}
    @endforeach

    {{ $slot ?? '' }}
</div>
