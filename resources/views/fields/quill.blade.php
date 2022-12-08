<div id="quill_{{ $field->id() }}">
    {!! $field->formViewValue($item) ?? '' !!}
</div>

<script>
    var quill = new Quill('#quill_{{ $field->id() }}', {
        theme: 'snow'
    });
</script>
