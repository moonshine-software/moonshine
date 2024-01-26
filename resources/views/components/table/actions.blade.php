@props([
    'actions',
    'simple' => false
])

<x-moonshine::action-group
    @class([
        'flex-nowrap',
        'justify-end' => ! $simple,
        'justify-center' => $simple,
    ])
    :actions="$actions"
/>
