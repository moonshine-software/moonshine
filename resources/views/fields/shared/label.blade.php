<label for="{{ $element->name() }}">
    {{ $element->label()  }} {!! $element->isRequired() ? "<span class='text-pink'>*</span>" : ""  !!}
</label>
