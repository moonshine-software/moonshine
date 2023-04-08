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

$locked = method_exists($element, 'isLocked')
    && !in_array($element->attributes()->get('type'), ['checkbox', 'radio', 'color'])
    ? $element->isLocked()
    : false;
@endphp

@if($ext || $locked)
    <div class="form-group form-group-expansion"
         x-data="{ isLock: true, toggle() { this.isLock = ! this.isLock } }"
    >
        <x-moonshine::form.input
            :attributes="$element->attributes()->merge([
                'id' => $element->id(),
                'placeholder' => $element->label() ?? '',
                'name' => $element->name(),
                'value' => $value
            ])"
            x-bind:readonly="isLock"
            @class(['form-invalid' => $errors->has($element->name())])
        />
        @if($ext)
            <span class="expansion">{{ $ext }}</span>
        @endif
        @if($locked)
            <button @click.prevent="toggle()" class="expansion">
                <span x-show="isLock">
                    <x-moonshine::icon
                        icon="heroicons.lock-closed"
                        size="4"
                    />
                </span>
                <span x-show="!isLock">
                    <x-moonshine::icon
                        icon="heroicons.lock-open"
                        size="4"
                    />
                </span>
            </button>
        @endif
    </div>
@else
<x-moonshine::form.input
    :attributes="$element->attributes()->merge([
            'id' => $element->id(),
            'placeholder' => $element->label() ?? '',
            'name' => $element->name(),
            'value' => $value
        ])"
    @class(['form-invalid' => $errors->has($element->name())])
/>
@endif
