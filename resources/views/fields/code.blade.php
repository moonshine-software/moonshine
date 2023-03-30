<div id="flask_{{ $element->id() }}"
     class="w-100 relative border"
     style="min-height: 300px">
</div>

<input type="hidden"
       id="{{ $element->id() }}"
       name="{{ $element->name() }}"
       value="{{ $element->formViewValue($item) ?? '' }}"
/>

<script>
  (function() {
    document.addEventListener("DOMContentLoaded", function(event) {
      const input = document.getElementById('{{ $element->id() }}');

      const flask = new CodeFlask('#flask_{{ $element->id() }}', {
        lineNumbers: {{ $element->lineNumbers ? 'true' : 'false' }},
        language: '{{ $element->language }}',
        readonly: {{ $element->isReadonly() ? 'true' : 'false' }},
      });

      flask.onUpdate((code) => {
        input.value = code;
      });

      flask.updateCode(input.value);
    });
  })();
</script>

