<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\{Components,
    Layout\Block,
    Layout\Body,
    Layout\Content,
    Layout\Flash,
    Layout\Html,
    Layout\LayoutBuilder,
    Layout\Wrapper,
    Title};

class AppLayout extends BaseLayout
{
    protected function menu(): array
    {
        return [
            MenuGroup::make(static fn () => __('moonshine::ui.resource.system'), [
                MenuItem::make(
                    static fn () => __('moonshine::ui.resource.admins_title'),
                    MoonShineUserResource::class
                ),
                MenuItem::make(
                    static fn () => __('moonshine::ui.resource.role_title'),
                    MoonShineUserRoleResource::class
                ),
            ]),
        ];
    }

    public function build(): LayoutBuilder
    {
        return LayoutBuilder::make([
            Html::make([
                $this->getHeadComponent(),
                Body::make([
                    Wrapper::make([
                        // $this->getTopBarComponent(),
                        $this->getSidebarComponent(),

                        Block::make([
                            Flash::make(),

                            $this->getHeaderComponent(),

                            Content::make([
                                Title::make($this->getPage()->getTitle())->class('mb-6'),
                                Components::make(
                                    $this->getPage()->getComponents()
                                ),
                            ]),

                            $this->getFooterComponent(),
                        ])->class('layout-page'),
                    ]),
                ]),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
