<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Actions\ExportAction;
use MoonShine\Actions\FiltersAction;
use MoonShine\Components\ActionGroup;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Modal;
use MoonShine\Decorations\Offcanvas;
use MoonShine\Decorations\TextBlock;
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
                        ActionButton::make('Добавить', to_page($this->getResource(), FormPage::class))
                            ->customAttributes(['class' => 'btn btn-primary'])
                            ->icon('heroicons.outline.plus'),

                        Modal::make('Добавить No Async')
                            ->content('Title', 'Hello world')
                            ->customAttributes(['class' => 'btn btn-primary'])
                            ->icon('heroicons.outline.plus'),

                        Modal::make('Добавить Async')
                            ->async($this->route())
                            ->wide()
                            ->content('Title', 'Hello world')
                            ->customAttributes(['class' => 'btn btn-primary'])
                            ->icon('heroicons.outline.plus'),

                        Offcanvas::make('Offcanvas')
                            ->content('Title', 'Hello world')
                            ->customAttributes(['class' => 'btn btn-primary'])
                            ->icon('heroicons.outline.plus'),

                        ActionGroup::make([
                            ExportAction::make('Export')
                                ->setResource($this->getResource())
                                ->showInDropdown(),
                        ]),
                    ])->justifyAlign('start'),

                    ActionGroup::make([
                        FiltersAction::make('Filters')
                            ->filters([
                                Text::make('Test'),
                            ])
                            ->setResource($this->getResource())
                            ->showInLine(),
                    ]),

                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap',
                ]),
            ]),
            table()->fields($this->getResource()->getIndexFields()->toArray())
                ->cast($this->getResource()->getModelCast())
                ->items($items->items())
                ->paginator($items)
                ->trAttributes(fn ($data, int $index, ComponentAttributeBag $attributes): ComponentAttributeBag => $attributes->when(
                    $index === 0,
                    fn (ComponentAttributeBag $attr): ComponentAttributeBag => $attr->merge([
                        'class' => 'bgc-purple',
                    ])
                ))
                ->tdAttributes(fn ($data, int $cell, int $index, ComponentAttributeBag $attributes): ComponentAttributeBag => $attributes->when(
                    $index === 1 && $cell === 0,
                    fn (ComponentAttributeBag $attr): ComponentAttributeBag => $attr->merge([
                        'class' => 'bgc-red',
                    ])
                ))
                ->buttons([
                    ActionButton::make(
                        '',
                        url: fn ($data): string => route('moonshine.page', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'pageUri' => 'form-page',
                        'resourceItem' => $data->getKey(),
                    ])
                    )
                        ->customAttributes(['class' => 'btn-purple'])
                        ->icon('heroicons.outline.pencil')
                        ->showInLine(),


                    ActionButton::make(
                        '',
                        url: fn ($data): string => route('moonshine.crud.destroy', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'resourceItem' => $data->getKey(),
                    ])
                    )
                        ->customAttributes(['class' => 'btn-pink'])
                        ->icon('heroicons.outline.trash')
                        ->inModal(
                            fn(): array|string|null => __('moonshine::ui.delete'),
                            fn(ActionButton $action): string => (string) form(
                                $action->url(),
                                fields: [
                                    Hidden::make('_method')->setValue('DELETE'),
                                    TextBlock::make('', __('moonshine::ui.confirm_delete'))
                                ]
                            )->submit(__('moonshine::ui.delete'), ['class' => 'btn-pink'])
                        )
                        ->showInLine(),


                    ActionButton::make(
                        '',
                        url: fn (): string => route('moonshine.crud.destroy', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'resourceItem' => 0,
                    ])
                    )
                        ->bulk()
                        ->customAttributes(['class' => 'btn-pink'])
                        ->icon('heroicons.outline.trash')
                        ->inModal(
                            fn(): string => 'Delete',
                            fn(): string => (string) form($this->getResource()->route('massDelete'))
                                ->fields([
                                    Hidden::make('_method')->setValue('DELETE'),
                                    Hidden::make('ids')->customAttributes([
                                        'class' => 'actionsCheckedIds',
                                    ]),
                                    Heading::make(__('moonshine::ui.confirm_delete')),
                                ])
                                ->submit('Delete', ['class' => 'btn-pink'])
                        )
                        ->showInLine(),

                ]),
        ];
    }
}
