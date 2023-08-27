<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\FiltersButton;
use MoonShine\Buttons\IndexPage\FormButton;
use MoonShine\Buttons\IndexPage\MassDeleteButton;
use MoonShine\Buttons\IndexPage\ShowButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
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

                        ActionButton::make(__('moonshine::ui.create'), to_page($resource, 'form-page', ['_fragment-load' => 'form']))
                            ->customAttributes(['class' => 'btn btn-primary'])
                            ->icon('heroicons.outline.plus')
                            ->inModal(
                                fn (): array|string|null => __('moonshine::ui.create'),
                                fn (): string => '',
                                async: true
                            ),


                        ActionButton::make(__('moonshine::ui.create'), to_page($resource, 'form-page'))
                            ->customAttributes(['class' => 'btn btn-primary'])
                            ->icon('heroicons.outline.plus'),
                    ])->justifyAlign('start'),

                    ActionGroup::make([
                        FiltersButton::for($resource),
                    ]),
                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap',
                ]),
            ]),

            Fragment::make([
                TableBuilder::make(items: $items)
                    ->fields($resource->getIndexFields())
                    ->cast($resource->getModelCast())
                    ->withNotFound()
                    ->buttons([
                        ShowButton::for($resource),
                        FormButton::for($resource),
                        DeleteButton::for($resource),
                        MassDeleteButton::for($resource),
                    ]),
            ])->withName('table'),
        ];
    }
}
