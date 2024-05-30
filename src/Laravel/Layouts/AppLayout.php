<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Core\Contracts\PageContract;
use MoonShine\Laravel\Components\Layout\Flash;
use MoonShine\Laravel\Components\Layout\Locales;
use MoonShine\Laravel\Components\Layout\Notifications;
use MoonShine\Laravel\Components\Layout\Profile;
use MoonShine\Laravel\Components\Layout\Search;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\Breadcrumbs;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Layout\Block;
use MoonShine\UI\Components\Layout\Body;
use MoonShine\UI\Components\Layout\Burger;
use MoonShine\UI\Components\Layout\Content;
use MoonShine\UI\Components\Layout\Footer;
use MoonShine\UI\Components\Layout\Head;
use MoonShine\UI\Components\Layout\Header;
use MoonShine\UI\Components\Layout\Html;
use MoonShine\UI\Components\Layout\LayoutBuilder;
use MoonShine\UI\Components\Layout\Logo;
use MoonShine\UI\Components\Layout\Menu;
use MoonShine\UI\Components\Layout\Sidebar;
use MoonShine\UI\Components\Layout\ThemeSwitcher;
use MoonShine\UI\Components\Layout\Wrapper;
use MoonShine\UI\Components\Title;
use MoonShine\UI\Components\When;
use MoonShine\UI\MoonShineLayout;

class AppLayout extends MoonShineLayout
{
    protected function menu(): array
    {
        return [
            MenuGroup::make(static fn () => __('moonshine::ui.resource.system'), [
                MenuItem::make(
                    static fn () => __('moonshine::ui.resource.admins_title'),
                    moonshine()->getContainer(MoonShineUserResource::class)
                ),
                MenuItem::make(
                    static fn () => __('moonshine::ui.resource.role_title'),
                    moonshine()->getContainer(MoonShineUserRoleResource::class)
                ),
            ]),
        ];
    }

    public function build(PageContract $page): LayoutBuilder
    {
        $logo = moonshineAssets()->asset('vendor/moonshine/logo.svg');
        $logoSmall = moonshineAssets()->asset('vendor/moonshine/logo.svg');

        return LayoutBuilder::make([
            Html::make([
                Head::make()->title($page->title()),
                Body::make([
                    Wrapper::make([
                        Sidebar::make([
                            Block::make([
                                Block::make([
                                    Logo::make(
                                        moonshineRouter()->getEndpoints()->home(),
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
                                    static fn (): bool => moonshineConfig()->isAuthEnabled(),
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
                                    ->prepend(moonshineRouter()->getEndpoints()->home(), icon: 'home'),

                                Search::make(),

                                When::make(
                                    static fn (): bool => moonshineConfig()->isAuthEnabled() && moonshineConfig()->isUseNotifications(),
                                    static fn (): array => [Notifications::make()]
                                ),

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
