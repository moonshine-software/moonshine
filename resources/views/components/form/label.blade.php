<label {{ $attributes->merge(['class' => 'form-label'])->except('required') }}>
    {{ $slot ?? ''  }}

    @if($attributes->get('required', false))
        <span class="required">*</span>
    @endif
</label>
