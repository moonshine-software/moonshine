@props([
    'required' => false,
    'forName' => null,
])
<label
    @if($forName !== null)
        :for="$id('field-' + @js($forName))"
    @endif
    {{ $attributes->merge(['class' => 'form-label']) }}
>
    {{ $slot ?? ''  }}

    @if($required)
        <span class="required">*</span>
    @endif
</label>
