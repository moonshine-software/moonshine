@props([
    'components',
    'bulkButtons',
    'asyncUrl',
    'async' => false,
    'notfound' => false,
    'colSpan' => 12,
    'adaptiveColSpan' => 12,
    'name' => 'default',
    'translates' => [],
])

<div x-data="cardsBuilder(
    {{ (int) $async }},
    '{{ $asyncUrl }}'
)"
    data-pushstate="{{ $attributes->get('data-pushstate', false)}}"
    @defineEventWhen($async, 'cards_updated', $name, 'asyncRequest')
    {{ $attributes->only(['data-events'])}}
>
    <x-moonshine::loader x-show="loading" />
    <div x-show="!loading">
        @if($components->isNotEmpty())
            <x-moonshine::layout.grid>
                @foreach($components as $card)
                    <x-moonshine::layout.column :colSpan="$colSpan" :adaptiveColSpan="$adaptiveColSpan">
                        {!! $card !!}
                    </x-moonshine::layout.column>
                @endforeach
            </x-moonshine::layout.grid>

            @if($hasPaginator)
                {!! $paginator !!}
            @endif
        @else
            <x-moonshine::alert type="default" class="my-4" icon="s.no-symbol">
                {{ $translates['notfound'] }}
            </x-moonshine::alert>
        @endif
    </div>
</div>
