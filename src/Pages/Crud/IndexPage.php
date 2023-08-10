<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Actions\FiltersAction;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\FormButton;
use MoonShine\Buttons\IndexPage\MassDeleteButton;
use MoonShine\Buttons\IndexPage\ShowButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Pages\Page;

class IndexPage extends Page
{
    public function components(): array
    {
        $resource = $this->getResource();

        $items = $resource->paginate();

        return [
            Grid::make([
                Column::make([
                    Flex::make([
                        ActionButton::make(__('moonshine::ui.create'), to_page($resource, 'form-page'))
                            ->customAttributes(['class' => 'btn btn-primary'])
                            ->icon('heroicons.outline.plus'),
                    ])->justifyAlign('start'),

                    ActionGroup::make([
                        FiltersAction::make(__('moonshine::ui.filters'))
                            ->filters($resource->filters())
                            ->setResource($resource)
                            ->showInLine(),
                    ]),
                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap',
                ]),
            ]),

            TableBuilder::make(items: $items)
                ->fields($resource->getIndexFields()->toArray())
                ->cast($resource->getModelCast())
                ->buttons([
                    ShowButton::for($resource),
                    FormButton::for($resource),
                    DeleteButton::for($resource),
                    MassDeleteButton::for($resource),
                ]),
        ];
    }
}
