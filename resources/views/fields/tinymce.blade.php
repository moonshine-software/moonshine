@props([
    'config' => [],
])
<div class="tinymce">
    <x-moonshine::form.textarea
        :attributes="$element->attributes()->merge([
            'name' => $element->name()
        ])->except('x-bind:id')"
        ::id="$id('tiny-mce')"
        x-data="tinymce({{ collect($config)->toJson() }})"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>
