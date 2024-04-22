@props([
    'items' => [],
    'dropdown' => false
])

@if($items !== [])
    <ul {{ $attributes->class(['menu-inner' => !$dropdown, 'menu-inner-dropdown' => $dropdown]) }}>
        @foreach($items as $item)
            {!! $item !!}
        @endforeach
    </ul>
@endif

{{ $slot ?? '' }}
