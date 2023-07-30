<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use App\Http\Livewire\LiveWireTestComponent;
use App\View\Components\HelloWorldComponent;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Hidden;
use MoonShine\ItemActions\ItemAction;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Pages\Page;

class IndexPage extends Page
{
    public function components(): array
    {
        $items = $this->getResource()->paginate();

        return [
            Grid::make([
                Column::make([
                    Heading::make('Left'),
                    new HelloWorldComponent(),
                    new LiveWireTestComponent(),
                    TableBuilder::make(),
                    LineChartMetric::make(''),
                ])->columnSpan(6),

                Column::make([
                    Heading::make('Right'),
                    TableBuilder::make(),
                ])->columnSpan(6),
            ]),
            TableBuilder::make()->fields($this->getResource()->getFields()->onlyFields()->toArray())
                ->cast(get_class($this->getResource()->getModel()))
                ->items($items->items())
                ->paginator($items)
                ->trAttributes(function ($data, int $index, ComponentAttributeBag $attributes) {
                    return $attributes->when(
                        $index === 0 && $data->getKey() === 2,
                        fn (ComponentAttributeBag $attr) => $attr->merge([
                            'class' => 'bgc-purple',
                        ])
                    );
                })
                ->tdAttributes(function ($data, int $cell, int $index, ComponentAttributeBag $attributes) {
                    return $attributes->when(
                        $index === 1 && $cell === 0 && $data->getKey() === 1,
                        fn (ComponentAttributeBag $attr) => $attr->merge([
                            'class' => 'bgc-red',
                        ])
                    );
                })
                ->buttons([
                    ItemAction::make(
                        '', url: fn ($data) => route('moonshine.page', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'pageUri' => 'form-page',
                        'item' => $data->getKey(),
                    ])
                    )
                        ->customAttributes(['class' => 'btn-purple'])
                        ->icon('heroicons.outline.pencil')
                        ->showInLine(),


                    ItemAction::make(
                        '', url: fn ($data) => route('moonshine.crud.destroy', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'item' => $data->getKey(),
                    ])
                    )
                        ->customAttributes(['class' => 'btn-pink'])
                        ->icon('heroicons.outline.trash')
                        ->withConfirm()
                        ->showInLine(),


                    ItemAction::make(
                        '', url: fn () => route('moonshine.crud.destroy', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'item' => 0,
                    ])
                    )
                        ->bulk()
                        ->customAttributes(['class' => 'btn-pink'])
                        ->icon('heroicons.outline.trash')
                        ->withConfirm(
                            'Delete',
                            (string) FormBuilder::make()
                                ->fields([
                                    Hidden::make('ids')
                                ])
                                ->submit('Delete', ['class' => 'btn-red'])
                        )
                        ->showInLine(),

                ]),
        ];
    }
}