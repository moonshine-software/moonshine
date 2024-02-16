@props([
    'components',
    'bulkButtons',
    'asyncUrl',
    'async' => false,
    'simplePaginate' => false,
    'notfound' => false,
    'colSpan' => 12,
    'adaptiveColSpan' => 12,
    'name' => 'default'
])

<div x-data="cardsBuilder(
    {{ (int) $async }},
    '{{ $asyncUrl }}'
)"
    data-pushstate="{{ $attributes->get('data-pushstate', false)}}"
    @defineEventWhen($async, 'cards-updated', $name, 'asyncRequest')
    {{ $attributes->only(['data-events'])}}
>
    <x-moonshine::loader x-show="loading" />
    <div x-show="!loading">
        @if($components->isNotEmpty())
            <x-moonshine::grid>
                @foreach($components as $card)
                    @continue(!isSeeWhenExists($card))

                    <x-moonshine::column :colSpan="$colSpan" :adaptiveColSpan="$adaptiveColSpan">
                        {{ $card->render() }}
                    </x-moonshine::column>
                @endforeach
            </x-moonshine::grid>

            @if($hasPaginator)
                {{ $paginator->links(
                    $simplePaginate
                        ? 'moonshine::ui.simple-pagination'
                        : 'moonshine::ui.pagination',
                    ['async' => $async]
                ) }}
            @endif
        @else
            <x-moonshine::alert type="default" class="my-4" icon="heroicons.no-symbol">
                {{ trans('moonshine::ui.notfound') }}
            </x-moonshine::alert>
        @endif
    </div>
</div>
