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
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Fields\File;
use MoonShine\Pages\Page;

class IndexPage extends Page
{
    public function components(): array
    {
        $resource = $this->getResource();

        $items = $resource->paginate();

        $export = $resource->export();
        $import = $resource->import();

        return [
            Grid::make([
                Column::make([
                    Flex::make([
                        ActionButton::make(
                            __('moonshine::ui.create'),
                            to_page(
                                $resource,
                                'form-page',
                                fragment: 'crud-form'
                            )
                        )
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

                    ActionGroup::make()->when(
                        ! empty($resource->filters()),
                        fn (ActionGroup $group): ActionGroup => $group->add(FiltersButton::for($resource))
                    )->when(
                        ! is_null($export),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            ActionButton::make(
                                $export->label(),
                                $resource->route('handler', query: ['handlerUri' => $export->uriKey()])
                            )
                                ->customAttributes(['class' => 'btn btn-primary'])
                                ->icon($export->iconValue())
                        ),
                    )->when(
                        ! is_null($import),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            ActionButton::make(
                                $import->label(),
                                '#'
                            )
                                ->customAttributes(['class' => 'btn btn-primary'])
                                ->icon($import->iconValue())
                                ->inOffCanvas(
                                    fn () => $import->label(),
                                    fn (): FormBuilder => FormBuilder::make(
                                        $resource->route('handler', query: ['handlerUri' => $import->uriKey()])
                                    )
                                        ->fields([
                                            File::make(column: $import->getInputName())->required(),
                                        ])
                                        ->submit(__('moonshine::ui.confirm'))
                                )
                        ),
                    ),
                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap',
                ]),
            ]),

            LineBreak::make(),

            ActionGroup::make()->when(
                ! empty($resource->queryTags()),
                function (ActionGroup $group) use ($resource): ActionGroup {
                    foreach ($resource->queryTags() as $tag) {
                        $group->add(
                            ActionButton::make(
                                $tag->label(),
                                to_page($resource, IndexPage::class, params: ['queryTag' => $tag->uri()])
                            )
                                ->showInLine()
                                ->icon($tag->iconValue())
                                ->canSee(fn () => $tag->isSee(moonshineRequest()))
                                ->when(
                                    $tag->isActive(),
                                    fn (ActionButton $btn): ActionButton => $btn->customAttributes([
                                        'class' => 'btn-primary',
                                    ])
                                )
                        );
                    }

                    return $group;
                }
            ),

            LineBreak::make(),

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
            ])->withName('crud-table'),
        ];
    }
}
