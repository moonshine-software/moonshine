<div x-id="['has-many']" :id="$id('has-many')">
    <x-moonshine::divider />
    <x-moonshine::divider :label="$element->label()" />

    {{ $element->value()->render() }}
</div>
