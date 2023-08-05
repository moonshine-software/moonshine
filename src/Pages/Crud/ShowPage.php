<?php

namespace MoonShine\Pages\Crud;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\DetailCardBuilder;
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
            DetailCardBuilder::make()
                ->title('#'.$this->getResource()->getItem()->getKey())
                ->fillFromModelResource($this->getResource())
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
                ]),
        ];
    }
}