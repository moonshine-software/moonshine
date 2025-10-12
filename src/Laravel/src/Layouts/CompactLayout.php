<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\UI\Components\{Layout\Body,
    Layout\Content,
    Layout\Div,
    Layout\Flash,
    Layout\Html,
    Layout\Layout,
    Layout\Wrapper};

class CompactLayout extends AppLayout
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function withTitle(): bool
    {
        return false;
    }

    protected function withSubTitle(): bool
    {
        return false;
    }

    public function build(): Layout
    {
        return Layout::make([
            Html::make([
                $this->getHeadComponent(),
                Body::make([
                    Wrapper::make([
                        // $this->getTopBarComponent(),
                        $this->getSidebarComponent(),
                        Div::make([
                            Fragment::make([
                                Flash::make(),

                                $this->getHeaderComponent(),

                                Content::make($this->getContentComponents()),

                                $this->getFooterComponent(),
                            ])->class('layout-page')->name(self::CONTENT_FRAGMENT_NAME),
                        ])->class('layout-main')->customAttributes(['id' => self::CONTENT_ID]),
                    ]),
                ])->class('theme-compact'),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes($this->isAlwaysDark()),
        ]);
    }
}
