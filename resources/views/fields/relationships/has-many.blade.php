<div x-id="['has-many']"
     :id="$id('has-many')"
     data-field-block="{{ $element->column() }}"
>
    @if($element->isCreatable())
        <x-moonshine::divider />

        {{ $element->createButton() }}
    @endif

    <x-moonshine::divider />

    {{ $element->value(withOld: false)->render() }}
</div>
