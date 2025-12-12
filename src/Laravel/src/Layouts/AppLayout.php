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
    Layout\Wrapper,
    When};

class AppLayout extends BaseLayout
{
    protected bool $contentCentered = false;

    protected bool $contentSimpled = false;

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
                        When::make(
                            fn (): bool => $this->topBar,
                            fn (): array => [
                                $this->getTopBarComponent(),
                            ]
                        ),

                        When::make(
                            fn (): bool => $this->sidebar,
                            fn (): array => [
                                $this->getSidebarComponent(),
                            ]
                        ),

                        When::make(
                            fn (): bool => $this->secondBar,
                            fn (): array => [
                                $this->getSecondBarComponent(),
                            ]
                        ),

                        When::make(
                            fn (): bool => $this->mobileMode || $this->bottomBar,
                            fn (): array => [
                                $this->getBottomBarComponent(),
                            ]
                        ),

                        Div::make([
                            Fragment::make([
                                Flash::make(),

                                $this->getHeaderComponent(),

                                Content::make($this->getContentComponents()),

                                $this->getFooterComponent(),
                            ])->class(['layout-page', 'layout-page-simple' => $this->contentSimpled])->name(self::CONTENT_FRAGMENT_NAME),
                        ])->class(['layout-main', 'layout-main-centered' => $this->contentCentered])->customAttributes(['id' => self::CONTENT_ID]),
                    ]),
                ]),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->when(
                    $this->hasThemes() || $this->isAlwaysDark(),
                    fn (Html $html): Html => $html->withThemes($this->isAlwaysDark())
                ),
        ]);
    }
}
