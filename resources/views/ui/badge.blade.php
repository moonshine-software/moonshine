@if($value !== false)
    <x-moonshine::badge
        :color="$color"
        class="{{ $margin ?? false ? 'm-1' : '' }}"
    >
        @if($link ?? false)
            <x-moonshine::link-native href="{{ $link }}">
                {!! $value !!}
            </x-moonshine::link-native>
        @else
            {!! $value !!}
        @endif
    </x-moonshine::badge>
@else
    &mdash;
@endif
