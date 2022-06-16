<label for="{{ $field->name() }}">
    {{ $field->label()  }} {!! $field->isRequired() ? "<span class='text-pink'>*</span>" : ""  !!}
</label>