<div x-id="['json']"
     :id="$id('json')"
     {{ $element->attributes()->only('class') }}
     data-field-block="{{ $element->name() }}"
>
    {{ $table->render() }}
</div>
