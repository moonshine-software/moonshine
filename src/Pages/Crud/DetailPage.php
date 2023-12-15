<?php

namespace MoonShine\Pages\Crud;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\Buttons\DeleteButton;
use MoonShine\Buttons\EditButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\LineBreak;
use MoonShine\Enums\PageType;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use Throwable;

/**
 * @method ModelResource getResource()
 */
class DetailPage extends Page
{
    protected ?PageType $pageType = PageType::DETAIL;

    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();

        $breadcrumbs[$this->route()] = $this->getResource()->getItem()
            ?->{$this->getResource()->column()};

        return $breadcrumbs;
    }

    public function beforeRender(): void
    {
        abort_if(
            ! in_array('view', $this->getResource()->getActiveActions())
            || ! $this->getResource()->can('view'),
            403
        );
    }

    /**
     * @throws Throwable
     */
    public function components(): array
    {
        $this->validateResource();
        $item = $this->getResource()->getItem();

        if (! $item?->exists) {
            oops404();
        }

        return $this->getLayers();
    }

    /**
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        $resource = $this->getResource();
        $item = $resource->getItem();

        return [
            Block::make([
                Fragment::make([
                    TableBuilder::make(
                        $resource->getDetailFields()
                    )
                        ->cast($resource->getModelCast())
                        ->items([$item])
                        ->vertical()
                        ->simple()
                        ->preview()
                        ->tdAttributes(fn (
                            $data,
                            int $row,
                            int $cell,
                            ComponentAttributeBag $attributes
                        ): ComponentAttributeBag => $attributes->when(
                            $cell === 0,
                            fn (ComponentAttributeBag $attr): ComponentAttributeBag => $attr->merge([
                                'class' => 'font-semibold',
                                'width' => '20%',
                            ])
                        )),
                ])->name('crud-show-table'),

                LineBreak::make(),

                Flex::make([
                    ActionGroup::make([
                        ...$resource->getDetailButtons(),
                        EditButton::for($resource),
                        DeleteButton::for(
                            $resource,
                            redirectAfterDelete: $resource->redirectAfterDelete()
                        ),
                    ])
                        ->setItem($item),
                ])->justifyAlign('end'),
            ]),
        ];
    }

    /**
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        $components = [];
        $item = $this->getResource()->getItem();

        if (! $item?->exists) {
            return $components;
        }

        $outsideFields = $this->getResource()->getDetailFields(onlyOutside: true);

        if ($outsideFields->isNotEmpty()) {
            $components[] = LineBreak::make();

            foreach ($outsideFields as $field) {
                if ($field->toOne()) {
                    $field->forcePreview();
                }

                $components[] = LineBreak::make();

                $components[] = Fragment::make([
                    $field->resolveFill(
                        $item?->attributesToArray() ?? [],
                        $item
                    ),
                ])->name($field->getRelationName());
            }
        }

        return $components;
    }
}
