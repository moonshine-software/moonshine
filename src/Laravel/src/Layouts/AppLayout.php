<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\{Layout\Body,
    Layout\Content,
    Layout\Div,
    Layout\Flash,
    Layout\Html,
    Layout\Layout,
    Layout\Wrapper};

class AppLayout extends BaseLayout
{
    /**
     * @return list<MenuElementContract>
     */
    protected function menu(): array
    {
        return [
            MenuGroup::make(static fn () => __('moonshine::ui.resource.system'), [
                MenuItem::make(MoonShineUserResource::class),
                MenuItem::make(MoonShineUserRoleResource::class),
            ]),
        ];
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
                        Div::make()
                            ->class('layout-overlay')
                            ->customAttributes([
                                'x-cloak' => '',
                                'x-show' => 'asideMenuOpen',
                                'x-on:click' => 'asideMenuOpen = false',
                                'x-transition.opacity' => '',
                            ]),
                    ]),
                ]),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes($this->isAlwaysDark()),
        ]);
    }
}
