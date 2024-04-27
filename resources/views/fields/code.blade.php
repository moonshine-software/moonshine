@props([
    'label' => '',
    'value' => '',
    'lineNumbers' => false,
    'language' => 'php',
])
<div class="code-container">
    <div
        x-data="code({
        lineNumbers: {{ $lineNumbers ? 'true' : 'false' }},
        language: '{{ $language }}',
        readonly: {{ $attributes->get('readonly') ? 'true' : 'false' }},
    })"
        {{ $attributes
            ->only('class')
            ->merge(['class' => 'w-100 min-h-[300px] relative']) }}
    ></div>

    <x-moonshine::form.textarea
        style="display: none;"
        :attributes="$attributes->merge([
            'aria-label' => $label ?? '',
            'class' => 'code-source'
        ])"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>

