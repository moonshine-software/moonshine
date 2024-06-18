@props([
    'value' => ''
])
<div class="easyMde">
    <x-moonshine::form.textarea
        :attributes="$attributes->merge([
            'x-data' => 'easyMde'
        ])"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>
