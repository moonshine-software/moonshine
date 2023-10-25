@props([
    'actions',
    'simple' => false
])
<div {{ $attributes->class([
    'flex',
    'items-center',
    'justify-end' => !$simple,
    'justify-center' => $simple,
    'gap-2'
    ]) }}
>
    <x-moonshine::action-group
        :actions="$actions"
    />
</div>
