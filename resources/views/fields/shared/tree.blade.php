<div x-data="tree(@json($value?->modelKeys() ?? []))">
    {!! $html !!}
</div>
