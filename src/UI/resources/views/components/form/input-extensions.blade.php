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
            ->merge(['class' => 'form-group form-group-expansion'])
            ->merge($extensionsAttributes?->toArray() ?? [])
        }}>
        {{-- Prefix (left) --}}
        @if($prefixes->isNotEmpty())
            <div class="expansion-wrapper expansion-wrapper--prefix">
                @foreach($prefixes as $extension)
                    {!! $extension !!}
                @endforeach
            </div>
        @endif

        {{ $slot ?? '' }}

        {{-- Suffix/Others (right) --}}
        @if($others->isNotEmpty())
            <div class="expansion-wrapper">
                @foreach($others as $extension)
                    {!! $extension !!}
                @endforeach
            </div>
        @endif
    </div>
@else
    {{ $slot ?? '' }}
@endif
