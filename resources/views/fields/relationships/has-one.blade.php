<div x-id="['has-one']"
     :id="$id('has-one')"
     data-field-block="{{ $element->column() }}"
>
    <x-moonshine::divider />

    {{ $element->value(withOld: false)->render() }}
</div>
