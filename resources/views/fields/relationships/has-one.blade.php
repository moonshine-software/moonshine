<div x-id="['has-one']" :id="$id('has-one')">
    <x-moonshine::divider />
    <x-moonshine::divider :label="$element->label()" />

    {{ $element->value(withOld: false)->render() }}
</div>
