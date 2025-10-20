@props([
    'components' => [],
    'isWithoutSpace' => false,
    'itemsAlign' => 'center',
    'justifyAlign' => 'start',
])
<div
    {{ $attributes
        ->class([
            'sm:flex sm:flex-row',
            'space-y-4 sm:space-y-0 gap-4' => !$isWithoutSpace,
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
