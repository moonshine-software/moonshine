@props([
    'value' => '',
    'label' => '',
])
<div class="tinymce">
    <x-moonshine::form.textarea
        :attributes="$attributes->except('x-bind:id')"
        ::id="$id('tiny-mce')"
        x-data="tinymce()"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>
