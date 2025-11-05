@props([
    'searchable' => false,
    'loader' => true,
    'searchUrl' => '',
    'searchValue' => '',
    'searchPlaceholder' => '',
    'skeleton' => null,
    'topLeft' => null,
    'topRight' => null,
])

@if(($topLeft ?? false) || $searchable)
<x-moonshine::layout.flex justify-align="start">
    @if($searchable)
        <x-moonshine::form
            raw
            action="{{ $searchUrl }}"
            @submit.prevent="asyncFormRequest"
        >
            <x-moonshine::form.input
                name="search"
                type="search"
                value="{{ $searchValue }}"
                placeholder="{{ $searchPlaceholder }}"
            />
        </x-moonshine::form>
    @endif

    {!! $topLeft ?? '' !!}
</x-moonshine::layout.flex>
@endif

@if($topRight ?? false)
<x-moonshine::layout.flex justify-align="end">
    {!! $topRight ?? '' !!}
</x-moonshine::layout.flex>
@endif

@if($skeleton ?? false)
    <x-moonshine::skeleton x-show="loading">
        {!! $skeleton !!}
    </x-moonshine::skeleton>
@elseif($loader)
<x-moonshine::loader x-show="loading" />
@endif

<div @if(($skeleton ?? false) || $loader) x-show="!loading" @endif>
    {{ $slot }}
</div>
