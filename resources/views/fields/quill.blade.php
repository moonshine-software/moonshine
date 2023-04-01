<div>
    <div id="quill_{{ $element->id() }}" style="height: auto;">
        {!! $element->formViewValue($item) ?? '' !!}
    </div>
</div>

<script>
    var quill = new Quill('#quill_{{ $element->id() }}', {
        theme: 'snow'
    });
</script>

<style>
    .ql-container, .ql-toolbar {
        background-color: white!important;
        color: black!important;
    }
</style>
