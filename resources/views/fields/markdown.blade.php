@props([
    'value' => ''
])
<div class="easyMde">
    <x-moonshine::form.textarea
        :attributes="$element->attributes()->merge([
            'x-data' => 'easyMde'
        ])"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>
