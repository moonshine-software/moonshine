<?php

declare(strict_types=1);

namespace MoonShine\Layouts;

use MoonShine\Components\Breadcrumbs;
use MoonShine\Components\Components;
use MoonShine\Components\FlexibleRender;
use MoonShine\Components\Layout\Block;
use MoonShine\Components\Layout\Body;
use MoonShine\Components\Layout\Content;
use MoonShine\Components\Layout\Flash;
use MoonShine\Components\Layout\Footer;
use MoonShine\Components\Layout\Head;
use MoonShine\Components\Layout\Header;
use MoonShine\Components\Layout\Html;
use MoonShine\Components\Layout\LayoutBuilder;
use MoonShine\Components\Layout\Locales;
use MoonShine\Components\Layout\Menu;
use MoonShine\Components\Layout\Profile;
use MoonShine\Components\Layout\Search;
use MoonShine\Components\Layout\Sidebar;
use MoonShine\Components\Layout\TopBar;
use MoonShine\Components\Layout\Wrapper;
use MoonShine\Components\Title;
use MoonShine\Components\When;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\MoonShineLayout;
use MoonShine\MoonShineRequest;
use MoonShine\Pages\Page;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;

class AppLayout extends MoonShineLayout
{
    protected function menu(): array
    {
        return [
            MenuGroup::make(static fn () => __('moonshine::ui.resource.system'), [
                MenuItem::make(
                    static fn () => __('moonshine::ui.resource.admins_title'),
                    new MoonShineUserResource()
                ),
                MenuItem::make(
                    static fn () => __('moonshine::ui.resource.role_title'),
                    new MoonShineUserRoleResource()
                ),
            ])->canSee(fn (MoonShineRequest $request) => $request->isMoonShineRequest()),
        ];
    }

    public function build(Page $page): LayoutBuilder
    {
        return LayoutBuilder::make([
            Html::make([
                Head::make(),
                Body::make([
                    Wrapper::make([
                        TopBar::make([
                            Menu::make()->top(),
                        ])->hideLogo()->hideSwitcher(),
                        Sidebar::make([
                            Menu::make(),
                            When::make(
                                static fn () => config('moonshine.auth.enable', true),
                                static fn (): array => [Profile::make(withBorder: true)]
                            ),
                        ]),
                        Block::make([
                            Flash::make(),
                            Header::make([
                                Breadcrumbs::make($page->breadcrumbs())
                                    ->prepend(moonshineRouter()->home(), icon: 'heroicons.outline.home'),

                                Search::make(),
                                FlexibleRender::make(view('moonshine::layouts.shared.notifications')),
                                Locales::make(),
                            ]),

                            Content::make([
                                Title::make($page->title())->class('mb-6'),

                                Components::make(
                                    $page->getComponents()
                                ),
                            ]),

                            Footer::make()
                                ->copyright(fn (): string => sprintf(
                                    <<<'HTML'
                                        &copy; 2021-%d Made with ❤️ by
                                        <a href="https://cutcode.dev"
                                            class="font-semibold text-primary hover:text-secondary"
                                            target="_blank"
                                        >
                                            CutCode
                                        </a>
                                    HTML,
                                    now()->year
                                ))
                                ->menu([
                                    'https://moonshine-laravel.com/docs' => 'Documentation',
                                ]),
                        ])->class('layout-page'),
                    ]),
                ]),
            ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
