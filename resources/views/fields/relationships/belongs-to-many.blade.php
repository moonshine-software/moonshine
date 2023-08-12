<div x-id="['belongs-to-many']" :id="$id('belongs-to-many')">
    <x-moonshine::divider />
    <x-moonshine::divider :label="$element->label()" />

    {{ $element->value()->render() }}
</div>
