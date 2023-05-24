@props([
    'title' => '',
    'placement' => 'right',
    'trigger',
])
<span {{ $attributes }}
     x-data="tooltip(`{{ $slot }}`, {placement: '{{ $placement }}'})"
>
    {{ $trigger }}
</span>
