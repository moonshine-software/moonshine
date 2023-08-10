<?php

namespace MoonShine\Pages\Crud;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\ShowPage\FormButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Flex;
use MoonShine\Pages\Page;
use Throwable;

class ShowPage extends Page
{
    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();

        $breadcrumbs[$this->route()] = $this->getResource()
            ?->getItem()
            ?->{$this->getResource()->column()};

        return $breadcrumbs;
    }

    /**
     * @throws Throwable
     */
    public function components(): array
    {
        $resource = $this->getResource();

        return [
            Block::make([
                TableBuilder::make($resource->getFields()->onlyFields()->toArray())
                    ->cast($resource->getModelCast())
                    ->items([$resource->getItem()])
                    ->vertical()
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

                Divider::make(),

                Flex::make([
                    ActionGroup::make([
                        FormButton::for($resource)
                    ]),
                ])->justifyAlign('end'),
            ]),
        ];
    }
}
