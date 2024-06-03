@props([
    'components' => [],
    'isWithoutSpace' => false,
    'itemsAlign' => 'center',
    'justifyAlign' => 'start',
])
<div
    {{ $attributes
        ->class([
            'flex flex-col sm:flex-row',
            'gap-4' => !$isWithoutSpace,
            'items-' . $itemsAlign,
            'justify-' . $justifyAlign
        ])
    }}
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
