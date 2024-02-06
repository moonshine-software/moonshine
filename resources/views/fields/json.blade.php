<div x-id="['json']"
     :id="$id('json')"
     {{ $element->attributes()->only('class') }}
     data-field-block="{{ $element->column() }}"
>
    {{ $table->render() }}
</div>
