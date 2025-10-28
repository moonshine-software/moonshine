@props([
    'extensions' => null,
    'extensionsAttributes' => null,
])

@php
    $prefixClass = \MoonShine\UI\InputExtensions\InputPrefix::class;
    $prefixes = collect($extensions)->filter(fn($e) => $e instanceof $prefixClass);
    $others   = collect($extensions)->reject(fn($e) => $e instanceof $prefixClass);
@endphp

@if($prefixes->isNotEmpty() || $others->isNotEmpty())
    <div {{ $attributes
            ->class(['form-group-expansion--has-prefix' => $prefixes->isNotEmpty()])
            ->class(['form-group-expansion--has-suffix' => $others->isNotEmpty()])
            ->merge(['class' => 'form-group-expansion'])
            ->merge($extensionsAttributes?->toArray() ?? [])
        }}>

        {{ $slot ?? '' }}

        {{-- Prefix (left) --}}
        @if($prefixes->isNotEmpty())
            <div class="expansion-wrapper expansion-wrapper--prefix">
                @foreach($prefixes as $extension)
                    {!! $extension !!}
                @endforeach
            </div>
        @endif

        {{-- Suffix/Others (right) --}}
        @if($others->isNotEmpty())
            <div class="expansion-wrapper expansion-wrapper--suffix">
                @foreach($others as $extension)
                    {!! $extension !!}
                @endforeach
            </div>
        @endif
    </div>
@else
    {{ $slot ?? '' }}
@endif
