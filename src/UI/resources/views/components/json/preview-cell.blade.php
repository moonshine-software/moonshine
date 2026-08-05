@props([
    'field' => [],
])

@if(($field['type'] ?? null) === 'json')
    @if(($field['isEmpty'] ?? true) === true)
        @include('moonshine::fields.preview', ['value' => '-'])
    @else
        @include('moonshine::components.json.preview', [
            'items' => $field['items'] ?? [],
            'objectMode' => ($field['objectMode'] ?? false) === true,
            'tableMode' => ($field['tableMode'] ?? false) === true,
            'tableAttributes' => $field['tableAttributes'] ?? null,
            'tableBuilder' => $field['tableBuilder'] ?? null,
            'tableSimple' => ($field['tableSimple'] ?? false) === true,
            'tableSticky' => ($field['tableSticky'] ?? false) === true,
            'nested' => true,
        ])
    @endif
@elseif(($field['isBoolean'] ?? false) === true)
    <div class="flex min-h-5 items-center">
        <x-moonshine::boolean :value="($field['value'] ?? false) === true" />
    </div>
@elseif(($field['isEmpty'] ?? true) === true)
    @include('moonshine::fields.preview', ['value' => '-'])
@else
    @include('moonshine::fields.preview', ['value' => e($field['value'] ?? '')])
@endif
