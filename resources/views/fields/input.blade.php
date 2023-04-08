@php
if($element->isFile()) {
    $value = false;
} elseif(isset($valueKey)) {
    $value = is_array($element->formViewValue($item)) ? ($element->formViewValue($item)[$valueKey] ?? '') : '';
} else {
    $value = (string) $element->formViewValue($item);
}
$ext = method_exists($element, 'ext')
    && !in_array($element->attributes()->get('type'), ['checkbox', 'radio', 'color'])
    ? $element->ext()
    : false;
@endphp

@if($ext) <div class="form-group form-group-expansion"> @endif

<x-moonshine::form.input
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
        'value' => $value
    ])"
    @class(['form-invalid' => $errors->has($element->name())])
/>

@if($ext)
<span class="expansion">{{ $ext }}</span>
</div>
@endif
