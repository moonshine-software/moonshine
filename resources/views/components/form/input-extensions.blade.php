@props([
    'extensions' => null,
])
@if($extensions && $extensions->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'form-group form-group-expansion']) }}>
        {{ $slot ?? '' }}

        @foreach($extensions as $extension)
            {!! $extension->render() !!}
        @endforeach
    </div>
@else
    {{ $slot ?? '' }}
@endif
