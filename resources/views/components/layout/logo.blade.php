@props([
    'href',
    'logo',
    'logoAttributes',
    'logoSmall',
    'logoSmallAttributes',
])
<a {{ $attributes->merge(['class' => 'block', 'rel' => 'home', 'href' => $href]) }}>
    <img src="{{ $logo }}"
        {{ $logoAttributes?->merge([
            'class' => 'hidden h-14 xl:block',
        ]) }}
         alt="{{ $title }}"
    />

    @if($logoSmall)
        <img src="{{ $logoSmall }}"
            {{ $logoSmallAttributes?->merge(['class' => 'block h-8 lg:h-10 xl:hidden']) }}
             alt="{{ $title }}"
        />
    @endif
</a>
