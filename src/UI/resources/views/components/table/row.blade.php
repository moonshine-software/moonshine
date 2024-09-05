@props([
    'cells' => [],
])
<tr {{ $attributes }}>
    @foreach($cells as $td)
        {!! $td !!}
    @endforeach
</tr>
