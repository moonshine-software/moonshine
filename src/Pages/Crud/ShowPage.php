<?php

namespace MoonShine\Pages\Crud;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
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
        return [
            Block::make([
                TableBuilder::make($this->getResource()->getFields()->onlyFields()->toArray())
                    ->cast($this->getResource()->getModelCast())
                    ->items([$this->getResource()->getItem()])
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
                            'width' => '20%'
                        ])
                    )),

                Divider::make(),

                Flex::make([
                    ActionGroup::make([
                        ActionButton::make(
                            '',
                            url: fn (): string => route('moonshine.page', [
                                'resourceUri' => $this->getResource()->uriKey(),
                                'pageUri' => 'form-page',
                                'resourceItem' => request('resourceItem'),
                            ])
                        )
                            ->canSee(fn() => $this->getResource()->can('update'))
                            ->customAttributes(['class' => 'btn-purple'])
                            ->icon('heroicons.outline.pencil')
                            ->showInLine(),
                    ]),
                ])->justifyAlign('end')
            ]),
        ];
    }
}
