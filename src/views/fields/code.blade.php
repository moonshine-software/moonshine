<div id="flask_{{ $field->id() }}" class="w-100 relative border" style="min-height: 300px"></div>

<input type="hidden"
       id="{{ $field->id() }}"
       name="{{ $field->name() }}"
       value="{{ $field->formViewValue($item) ?? '' }}"
/>

<script>
  (function() {
    document.addEventListener("DOMContentLoaded", function(event) {
      const input = document.getElementById('{{ $field->id() }}');

      const flask = new CodeFlask('#flask_{{ $field->id() }}', {
        lineNumbers: {{ $field->lineNumbers ? 'true' : 'false' }},
        language: '{{ $field->language }}',
        readonly: {{ $field->isReadonly() ? 'true' : 'false' }},
      });

      flask.onUpdate((code) => {
        input.value = code;
      });

      flask.updateCode(input.value);
    });
  })();
</script>

