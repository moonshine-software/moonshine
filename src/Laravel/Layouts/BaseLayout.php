<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Laravel\Components\Layout\Locales;
use MoonShine\Laravel\Components\Layout\Notifications;
use MoonShine\Laravel\Components\Layout\Profile;
use MoonShine\Laravel\Components\Layout\Search;
use MoonShine\UI\AbstractLayout;
use MoonShine\UI\Components\Breadcrumbs;
use MoonShine\UI\Components\Layout\Assets;
use MoonShine\UI\Components\Layout\Block;
use MoonShine\UI\Components\Layout\Burger;
use MoonShine\UI\Components\Layout\Favicon;
use MoonShine\UI\Components\Layout\Footer;
use MoonShine\UI\Components\Layout\Head;
use MoonShine\UI\Components\Layout\Header;
use MoonShine\UI\Components\Layout\Logo;
use MoonShine\UI\Components\Layout\Menu;
use MoonShine\UI\Components\Layout\Meta;
use MoonShine\UI\Components\Layout\Sidebar;
use MoonShine\UI\Components\Layout\ThemeSwitcher;
use MoonShine\UI\Components\Layout\TopBar;
use MoonShine\UI\Components\When;

abstract class BaseLayout extends AbstractLayout
{
    protected function getHeadComponent(): Head
    {
        return Head::make([
            Meta::make()->customAttributes([
                'name' => 'csrf-token',
                'content' => csrf_token(),
            ]),
            Favicon::make()->bodyColor($this->getColorManager()->get('body')),
            Assets::make(),
        ])
            ->bodyColor($this->getColorManager()->get('body'))
            ->title($this->getPage()->getTitle());
    }

    protected function getLogoComponent(): Logo
    {
        return Logo::make(
            $this->getHomeUrl(),
            $this->getLogo(),
            $this->getLogo(small: true)
        );
    }

    protected function getSidebarComponent(): Sidebar
    {
        return Sidebar::make([
            Block::make([
                Block::make([
                    $this->getLogoComponent()->minimized(),
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
                    fn (): bool => $this->isAuthEnabled(),
                    static fn (): array => [Profile::make(withBorder: true)]
                ),
            ])->customAttributes([
                'class' => 'menu',
                ':class' => "asideMenuOpen && '_is-opened'",
            ]),
        ])->collapsed();
    }

    protected function getTopBarComponent(): Topbar
    {
        return TopBar::make([
            Block::make([
                $this->getLogoComponent()->minimized(),
            ])->class('menu-logo'),

            Block::make([
                Menu::make()->top(),
            ])->class('menu-navigation'),

            Block::make([
                When::make(
                    fn (): bool => $this->isAuthEnabled(),
                    static fn (): array => [Profile::make()]
                ),

                Block::make()->class('menu-inner-divider'),
                Block::make([
                    ThemeSwitcher::make()->top(),
                ])->class('menu-mode'),

                Block::make([
                    Burger::make(),
                ])->class('menu-burger'),
            ])->class('menu-actions'),
        ]);
    }

    protected function getHeaderComponent(): Header
    {
        return Header::make([
            Breadcrumbs::make($this->getPage()->getBreadcrumbs())->prepend($this->getHomeUrl(), icon: 'home'),
            Search::make(),
            When::make(
                fn (): bool => $this->isUseNotifications(),
                static fn (): array => [Notifications::make()]
            ),
            Locales::make(),
        ]);
    }

    protected function getFooterComponent(?array $menu = null, ?string $copyright = null): Footer
    {
        return Footer::make()
            ->copyright(static fn (): string => $copyright
                ?: sprintf(
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
            ->menu($menu
                ?: [
                    'https://moonshine-laravel.com/docs' => 'Documentation',
                ]);
    }

    protected function getHeadLang(): string
    {
        return str_replace('_', '-', app()->getLocale());
    }

    protected function getLogo(bool $small = false): string
    {
        $logo = $small ? 'logo-small.svg' : 'logo.svg';

        return $this->getAssetManager()->getAsset("vendor/moonshine/$logo");
    }

    protected function getHomeUrl(): string
    {
        return $this->getCore()->getRouter()->getEndpoints()->home();
    }

    protected function isAuthEnabled(): bool
    {
        return $this->getCore()->getConfig()->isAuthEnabled();
    }

    protected function isUseNotifications(): bool
    {
        return $this->isAuthEnabled() && $this->getCore()->getConfig()->useNotifications();
    }
}
