<textarea {{ $attributes->merge([
    'class' => 'form-textarea',
    'type' => 'text'])
    }}
>{{ $slot }}</textarea>
