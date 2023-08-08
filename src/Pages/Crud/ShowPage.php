<?php

namespace MoonShine\Pages\Crud;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Casts\ModelCast;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Pages\Page;
use Throwable;

class ShowPage extends Page
{
    /**
     * @throws Throwable
     */
    public function components(): array
    {
        return [
            Block::make([
                TableBuilder::make($this->getResource()->getFields()->onlyFields()->toArray())
                    ->cast(ModelCast::make($this->getResource()->getModel()::class))
                    ->items([$this->getResource()->getItem()])
                    ->vertical()
                    ->tdAttributes(fn ($data, int $cell, int $index, ComponentAttributeBag $attributes): ComponentAttributeBag => $attributes->when(
                        $cell % 2 === 0,
                        fn (ComponentAttributeBag $attr): ComponentAttributeBag => $attr->merge([
                            'class' => 'font-semibold',
                    ])
                )),
                ActionGroup::make([
                    ActionButton::make(
                        '',
                        url: fn (): string => route('moonshine.page', [
                            'resourceUri' => $this->getResource()->uriKey(),
                            'pageUri' => 'form-page',
                            'resourceItem' => request('resourceItem'),
                        ])
                    )
                        ->customAttributes(['class' => 'btn-purple'])
                        ->icon('heroicons.outline.pencil')
                        ->showInLine(),
                ])
            ])
        ];
    }
}