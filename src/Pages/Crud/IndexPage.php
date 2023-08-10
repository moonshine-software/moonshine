<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Actions\FiltersAction;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\TextBlock;
use MoonShine\Fields\Hidden;
use MoonShine\Pages\Page;

class IndexPage extends Page
{
    public function components(): array
    {
        $items = $this->getResource()->paginate();

        return [
            Grid::make([
                Column::make([
                    Flex::make([
                        ActionButton::make(__('moonshine::ui.create'), to_page($this->getResource(), 'form-page'))
                            ->customAttributes(['class' => 'btn btn-primary'])
                            ->icon('heroicons.outline.plus'),
                    ])->justifyAlign('start'),

                    ActionGroup::make([
                        FiltersAction::make(__('moonshine::ui.filters'))
                            ->filters($this->getResource()->filters())
                            ->setResource($this->getResource())
                            ->showInLine(),
                    ]),
                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap',
                ]),
            ]),

            TableBuilder::make()
                ->fields($this->getResource()->getIndexFields()->toArray())
                ->cast($this->getResource()->getModelCast())
                ->items($items->items())
                ->paginator($items)
                ->buttons([

                    ActionButton::make(
                        '',
                        url: fn ($data): string => to_page(
                            $this->getResource(),
                            'show-page',
                            ['resourceItem' => $data->getKey()]
                        )
                    )
                        ->icon('heroicons.outline.eye')
                        ->showInLine(),

                    ActionButton::make(
                        '',
                        url: fn ($data): string => to_page(
                            $this->getResource(),
                            'form-page',
                            ['resourceItem' => $data->getKey()]
                        )
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
                            fn (): array|string|null => __('moonshine::ui.delete'),
                            fn (ActionButton $action): string => (string) form(
                                $action->url(),
                                fields: [
                                    Hidden::make('_method')->setValue('DELETE'),
                                    TextBlock::make('', __('moonshine::ui.confirm_message')),
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
                            fn (): string => 'Delete',
                            fn (): string => (string) form($this->getResource()->route('massDelete'))
                                ->fields([
                                    Hidden::make('_method')->setValue('DELETE'),
                                    Hidden::make('ids')->customAttributes([
                                        'class' => 'actionsCheckedIds',
                                    ]),
                                    Heading::make(__('moonshine::ui.confirm_message')),
                                ])
                                ->submit('Delete', ['class' => 'btn-pink'])
                        )
                        ->showInLine(),
                ]),
        ];
    }
}
