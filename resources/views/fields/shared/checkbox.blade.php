<div>
    <input {!! $meta ?? '' !!}
           id="{{ $id }}"
           type="checkbox"
           name="{{ $name }}"
           value="{{ $value }}"
    />

    @if(isset($label) && $label)
        <label class="ml-5" for="{{ $id }}">{{ $label }}</label>
    @endif
</div>
