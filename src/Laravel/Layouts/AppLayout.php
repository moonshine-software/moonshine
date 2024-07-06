<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Laravel\Components\Layout\{Flash, Locales, Notifications, Profile, Search};
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\{Breadcrumbs,
    Components,
    Layout\Assets,
    Layout\Block,
    Layout\Body,
    Layout\Burger,
    Layout\Content,
    Layout\Favicon,
    Layout\Footer,
    Layout\Head,
    Layout\Header,
    Layout\Html,
    Layout\LayoutBuilder,
    Layout\Logo,
    Layout\Menu,
    Layout\Meta,
    Layout\Sidebar,
    Layout\ThemeSwitcher,
    Layout\Wrapper,
    Title,
    When};
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

    public function build(): LayoutBuilder
    {
        $logo = moonshineAssets()->getAsset('vendor/moonshine/logo.svg');
        $logoSmall = moonshineAssets()->getAsset('vendor/moonshine/logo.svg');

        return LayoutBuilder::make([
            Html::make([
                Head::make([
                    Meta::make()->customAttributes([
                        'name' => 'csrf-token',
                        'content' => csrf_token(),
                    ]),
                    Favicon::make(),
                    Assets::make(),
                ])->title($this->getPage()->getTitle()),
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
                                Breadcrumbs::make($this->getPage()->getBreadcrumbs())
                                    ->prepend(moonshineRouter()->getEndpoints()->home(), icon: 'home'),

                                Search::make(),

                                When::make(
                                    static fn (): bool => moonshineConfig()->isAuthEnabled() && moonshineConfig()->isUseNotifications(),
                                    static fn (): array => [Notifications::make()]
                                ),

                                Locales::make(),
                            ]),

                            Content::make([
                                Title::make($this->getPage()->getTitle())->class('mb-6'),

                                Components::make(
                                    $this->getPage()->getComponents()
                                ),
                            ]),

                            Footer::make()
                                ->copyright(static fn (): string => sprintf(
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
                ->customAttributes([
                    'lang' => str_replace('_', '-', app()->getLocale()),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
