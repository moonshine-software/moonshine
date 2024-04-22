<div x-id="['has-many']"
     :id="$id('has-many')"
>
    @if($element->isCreatable())
        <x-moonshine::divider />

        {!! $element->createButton() !!}
    @endif

    <x-moonshine::divider />

    {{ $table->render() }}
</div>
