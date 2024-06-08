@props([
    'cells' => [],
])
<tr {{ $attributes }}>
    @foreach($cells as $td)
        {!! $td->render() !!}
    @endforeach
</tr>
