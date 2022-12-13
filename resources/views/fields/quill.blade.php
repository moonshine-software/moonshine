<div id="quill_{{ $field->id() }}" style="height: auto;">
    {!! $field->formViewValue($item) ?? '' !!}
</div>

<script>
    var quill = new Quill('#quill_{{ $field->id() }}', {
        theme: 'snow'
    });
</script>

<style>
    .ql-container, .ql-toolbar {
        background-color: white!important;
        color: black!important;
    }
</style>
