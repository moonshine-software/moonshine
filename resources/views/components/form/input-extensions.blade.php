@props([
    'extensions' => null,
])
@if($extensions && $extensions->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'form-group form-group-expansion']) }}
         x-init="{!! trim($extensions->implode(fn($extension) => $extension->xInit()->implode(';'), ';'), ';') !!}"
         x-data="{ {!! trim($extensions->implode(fn($extension) => $extension->xData()->implode(','), ','), ',') !!} }"
    >
        {{ $slot }}

        @foreach($extensions as $extension)
            <x-dynamic-component
                :component="$extension->getView()"
                :extension="$extension"
            />
        @endforeach
    </div>
@else
    {{ $slot }}
@endif
