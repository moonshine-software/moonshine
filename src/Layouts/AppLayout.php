<?php

declare(strict_types=1);

namespace MoonShine\Layouts;

use MoonShine\Components\Breadcrumbs;
use MoonShine\Components\Components;
use MoonShine\Components\Layout\Block;
use MoonShine\Components\Layout\Body;
use MoonShine\Components\Layout\Burger;
use MoonShine\Components\Layout\Content;
use MoonShine\Components\Layout\Flash;
use MoonShine\Components\Layout\Footer;
use MoonShine\Components\Layout\Head;
use MoonShine\Components\Layout\Header;
use MoonShine\Components\Layout\Html;
use MoonShine\Components\Layout\LayoutBuilder;
use MoonShine\Components\Layout\Locales;
use MoonShine\Components\Layout\Logo;
use MoonShine\Components\Layout\Menu;
use MoonShine\Components\Layout\Notifications;
use MoonShine\Components\Layout\Profile;
use MoonShine\Components\Layout\Search;
use MoonShine\Components\Layout\Sidebar;
use MoonShine\Components\Layout\ThemeSwitcher;
use MoonShine\Components\Layout\Wrapper;
use MoonShine\Components\Title;
use MoonShine\Components\When;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\MoonShineLayout;
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
                MenuGroup::make('Group', [
                    MenuItem::make(
                        static fn () => __('moonshine::ui.resource.role_title'),
                        new MoonShineUserRoleResource()
                    ),

                    MenuGroup::make('Group 2', [
                        MenuItem::make(
                            static fn () => __('moonshine::ui.resource.role_title'),
                            new MoonShineUserRoleResource()
                        ),
                    ]),
                ]),
            ]),
        ];
    }

    public function build(Page $page): LayoutBuilder
    {
        $logo = asset('vendor/moonshine/logo.svg');
        $logoSmall = asset('vendor/moonshine/logo.svg');

        return LayoutBuilder::make([
            Html::make([
                Head::make(),
                Body::make([
                    Wrapper::make([
                        Sidebar::make([
                            Block::make([
                                Block::make([
                                    Logo::make(
                                        moonshineRouter()->home(),
                                        $logo,
                                        $logoSmall
                                    )->minimized(),
                                ])->class('menu-heading-logo'),

                                Block::make([
                                    Block::make([
                                        ThemeSwitcher::make(),
                                    ])->class('menu-heading-mode'),

                                    Block::make([
                                        Burger::make(),
                                    ])->class('menu-heading-burger'),
                                ])->class('menu-heading-actions'),
                            ])->class('menu-heading'),

                            Block::make([
                                Menu::make(),
                                When::make(
                                    static fn () => config('moonshine.auth.enable', true),
                                    static fn (): array => [Profile::make(withBorder: true)]
                                ),
                            ])->customAttributes([
                                'class' => 'menu',
                                ':class' => "asideMenuOpen && '_is-opened'",
                            ]),
                        ])->collapsed(),

                        Block::make([
                            Flash::make(),
                            Header::make([
                                Breadcrumbs::make($page->breadcrumbs())
                                    ->prepend(moonshineRouter()->home(), icon: 'heroicons.outline.home'),

                                Search::make(),
                                Notifications::make(),
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
