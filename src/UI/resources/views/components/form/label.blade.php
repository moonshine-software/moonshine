@props([
    'required' => false
])
<label {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $slot ?? ''  }}

    @if($required)
        <span class="required">*</span>
    @endif
</label>
