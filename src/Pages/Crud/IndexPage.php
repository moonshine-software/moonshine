<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Actions\ExportAction;
use MoonShine\Actions\FiltersAction;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Button;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Text;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Pages\Page;

class IndexPage extends Page
{
    public function components(): array
    {
        $items = $this->getResource()->paginate();

        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Metric 1')->value(200),
                ])->columnSpan(6),

                Column::make([
                    ValueMetric::make('Metric 2')->value(300),
                ])->columnSpan(6),

                Column::make([
                    Flex::make([
                        Button::make('Добавить', $this->route())
                            ->icon('heroicons.outline.plus'),

                        ActionGroup::make([
                            ExportAction::make('Export')
                                ->setResource($this->getResource())
                                ->showInDropdown()
                        ]),
                    ])->justifyAlign('start'),

                    ActionGroup::make([
                        FiltersAction::make('Filters')
                            ->filters([
                                Text::make('Test')
                            ])
                            ->setResource($this->getResource())
                            ->showInLine()
                    ]),

                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap'
                ]),
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
                    ActionButton::make(
                        '', url: fn ($data) => route('moonshine.page', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'pageUri' => 'form-page',
                        'item' => $data->getKey(),
                    ])
                    )
                        ->customAttributes(['class' => 'btn-purple'])
                        ->icon('heroicons.outline.pencil')
                        ->showInLine(),


                    ActionButton::make(
                        '', url: fn ($data) => route('moonshine.crud.destroy', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'item' => $data->getKey(),
                    ])
                    )
                        ->customAttributes(['class' => 'btn-pink'])
                        ->icon('heroicons.outline.trash')
                        ->withConfirm()
                        ->showInLine(),


                    ActionButton::make(
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
                                    Hidden::make('ids')->customAttributes([
                                        'class' => 'actionsCheckedIds'
                                    ])
                                ])
                                ->submit('Delete', ['class' => 'btn-pink'])
                        )
                        ->showInLine(),

                ]),
        ];
    }
}