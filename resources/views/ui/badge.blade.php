@if($value !== false)
    <x-moonshine::badge :color="$color">
        {!! $value !!}
    </x-moonshine::badge>
@else
    &mdash;
@endif
