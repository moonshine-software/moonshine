<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\AssetManager\InlineCss;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Crud\Components\Layout\Locales;
use MoonShine\Crud\Components\Layout\Notifications;
use MoonShine\Crud\Components\Layout\Search;
use MoonShine\Crud\Layouts\AbstractLayout;
use MoonShine\Laravel\Components\Layout\Profile;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Breadcrumbs;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Assets;
use MoonShine\UI\Components\Layout\BottomBar;
use MoonShine\UI\Components\Layout\Burger;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Favicon;
use MoonShine\UI\Components\Layout\Footer;
use MoonShine\UI\Components\Layout\Head;
use MoonShine\UI\Components\Layout\Header;
use MoonShine\UI\Components\Layout\Logo;
use MoonShine\UI\Components\Layout\Menu;
use MoonShine\UI\Components\Layout\Meta;
use MoonShine\UI\Components\Layout\SecondBar;
use MoonShine\UI\Components\Layout\Sidebar;
use MoonShine\UI\Components\Layout\ThemeSwitcher;
use MoonShine\UI\Components\Layout\TopBar;
use MoonShine\UI\Components\Title;
use MoonShine\UI\Components\When;

/**
 * @extends AbstractLayout<MoonShine>
 */
abstract class BaseLayout extends AbstractLayout
{
    public const CONTENT_FRAGMENT_NAME = '_content';

    public const CONTENT_ID = '_moonshine-content';

    protected bool $topBar = false;

    protected bool $secondBar = false;

    protected bool $bottomBar = false;

    protected bool $sidebar = true;

    protected bool $mobileMode = false;

    protected function getFaviconComponent(): Favicon
    {
        return Favicon::make();
    }

    protected function assets(): array
    {
        if ($this->mobileMode) {
            return [
                ...parent::assets(),

                InlineCss::make(
                    <<<CSS
                        :root {
                            --spacing: 0.25rem;
                            --text-xs: 14px;
                            --text-sm: 14px;
                            --ms-btn-icon-size:16px;
                        }
                        CSS,
                ),
            ];
        }

        return parent::assets();
    }

    protected function getHeadComponent(bool $withAssetsFragment = true): Head
    {
        return Head::make([
            Meta::make()->customAttributes([
                'name' => 'csrf-token',
                'content' => csrf_token(),
            ]),
            $this->getFaviconComponent()->bodyColor($this->getColorManager()->get('body')),

            $withAssetsFragment ?
                Fragment::make([
                    Assets::make(),
                ])->name('assets') : Assets::make(),
        ])
            ->bodyColor($this->getColorManager()->get('body'))
            ->title($this->getPage()->getTitle() ?: $this->getCore()->getConfig()->getTitle());
    }

    protected function getLogoComponent(): Logo
    {
        return Logo::make(
            $this->getHomeUrl(),
            $this->getLogo(),
            $this->getLogo(small: true),
        );
    }

    protected function getProfileComponent(): Profile
    {
        return Profile::make()->menu([
            ActionButton::make(
                label: $this->getCore()->getTranslator()->get('moonshine::ui.profile'),
                url: $this->getCore()->getRouter()->getEndpoints()->toPage(
                    $this->getCore()->getConfig()->getPage('profile', ProfilePage::class),
                ),
            )->icon('user'),
        ]);
    }

    /**
     * @return list<ComponentContract>
     */
    protected function sidebarSlot(): array
    {
        return [];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function sidebarTopSlot(): array
    {
        return [];
    }

    protected function getSecondBarComponent(): SecondBar
    {
        return SecondBar::make([
            Menu::make($this->getPage()->getMenu()),
        ])->collapsed();
    }

    protected function getBottomBarComponent(): BottomBar
    {
        return BottomBar::make([
            Menu::make()->top(),
        ]);
    }

    protected function getSidebarComponent(): Sidebar
    {
        return Sidebar::make([
            Fragment::make([
                Div::make([
                    $this->getLogoComponent()->minimized(),
                ])->class('menu-logo'),
                Div::make([
                    When::make(
                        fn (): bool => $this->isUseNotifications(),
                        static fn (): array => [Notifications::make()],
                    ),
                    When::make(
                        fn (): bool => $this->hasThemes() && ! $this->isAlwaysDark(),
                        static fn (): array => [ThemeSwitcher::make()],
                    ),
                    ...$this->sidebarTopSlot(),
                ])->class('menu-actions'),
                Div::make(array_filter([
                    $this->mobileMode ? null : Burger::make()->sidebar(),
                ]))->class('menu-burger'),
            ])->class('menu-header')->name('sidebar-top'),

            Fragment::make([
                ...$this->sidebarSlot(),
                Menu::make(),
            ])->class('menu menu--vertical')->name('sidebar-content'),
        ])->collapsed($this->secondBar === false);
    }

    /**
     * @return list<ComponentContract>
     */
    protected function topBarSlot(): array
    {
        return [];
    }

    protected function getTopBarComponent(): Topbar
    {
        return TopBar::make([
            Fragment::make([
                $this->getLogoComponent()->minimized(),
            ])->class('menu-logo')->name('topbar-logo'),

            Fragment::make([
                Menu::make()->top(),
            ])->class('menu menu--horizontal')->name('topbar-menu'),

            Fragment::make([
                ...$this->topBarSlot(),
                When::make(
                    fn (): bool => $this->isProfileEnabled(),
                    fn (): array
                        => [
                        $this->getProfileComponent(),
                    ],
                ),

                Div::make()->class('menu-divider menu-divider--vertical'),
                When::make(
                    fn (): bool => $this->hasThemes() && ! $this->isAlwaysDark(),
                    static fn (): array => [ThemeSwitcher::make()],
                ),
                Div::make(array_filter([
                    $this->mobileMode ? null : Burger::make()->topbar(),
                ]))->class('menu-burger'),
            ])->class('menu-actions')->name('topbar-actions'),
        ]);
    }

    protected function getHeaderComponent(): Header
    {
        $homeLabel = $this->getCore()->getTranslator()->get('moonshine::ui.home');

        if ($homeLabel === 'moonshine::ui.home') {
            $homeLabel = 'Home';
        }

        return Header::make([
            Div::make(array_filter([
                $this->mobileMode || ! $this->sidebar ? null : Burger::make(),
            ]))->class('menu-burger'),
            Breadcrumbs::make(
                $this->getPage()->getBreadcrumbs(),
            )->prepend(
                $this->getHomeUrl(),
                label: $homeLabel,
            ),
            $this->getSearchComponent(),
            When::make(
                fn (): bool => $this->hasThemes() && ! $this->isAlwaysDark() && ($this->mobileMode || (! $this->sidebar && ! $this->topBar)),
                static fn (): array => [ThemeSwitcher::make(),],
            ),
            Locales::make(),
            When::make(
                fn (): bool => $this->isProfileEnabled(),
                fn (): array
                    => [
                    Fragment::make([
                        $this->getProfileComponent(),
                    ])->name('profile'),
                ],
            ),
            When::make(
                fn (): bool => $this->isUseNotifications() && ($this->mobileMode || ! $this->sidebar),
                static fn (): array => [Notifications::make()],
            ),
        ]);
    }

    protected function getSearchComponent(): ComponentContract
    {
        return Search::make();
    }

    protected function getFooterMenu(): array
    {
        return [
            'https://getmoonshine.app/docs' => 'Documentation',
        ];
    }

    protected function getFooterCopyright(): string
    {
        return \sprintf(
            <<<'HTML'
                &copy; 2021-%d Made with ❤️ by
                <a href="https://cutcode.dev"
                    class="font-semibold text-primary"
                    target="_blank"
                >
                    CutCode
                </a>
                HTML,
            now()->year,
        );
    }

    protected function getFooterComponent(): Footer
    {
        return Footer::make()
            ->copyright($this->getFooterCopyright())
            ->menu($this->getFooterMenu());
    }

    protected function getHeadLang(): string
    {
        return str_replace('_', '-', app()->getLocale());
    }

    protected function getLogo(bool $small = false): string
    {
        $logo = $small ? 'logo-small.svg' : 'logo.svg';

        return $this->getAssetManager()->getAsset(
            $this->getCore()->getConfig()->getLogo($small) ?? "/vendor/moonshine/$logo",
        );
    }

    /**
     * @return list<ComponentContract>
     */
    protected function getContentComponents(): array
    {
        $components = [
            Components::make(
                $this->getPage()->getComponents(),
            ),
        ];

        if ($this->withTitle()) {
            $hasSubtitle = $this->withSubTitle() && $this->getPage()->getSubtitle() !== '';

            return array_filter([
                Title::make($this->getPage()->getTitle())->class($hasSubtitle ? '' : 'mb-6'),
                $hasSubtitle ? Heading::make($this->getPage()->getSubtitle())->class('mb-6') : null,
                ...$components,
            ]);
        }

        return $components;
    }

    protected function getHomeUrl(): string
    {
        return $this->getCore()->getRouter()->getEndpoints()->home();
    }

    protected function withTitle(): bool
    {
        return true;
    }

    protected function withSubTitle(): bool
    {
        return true;
    }

    protected function isAuthEnabled(): bool
    {
        return $this->getCore()->getConfig()->isAuthEnabled();
    }

    protected function isProfileEnabled(): bool
    {
        return $this->getCore()->getConfig()->isUseProfile();
    }

    protected function isUseNotifications(): bool
    {
        return $this->isAuthEnabled() && $this->getCore()->getConfig()->isUseNotifications();
    }

    protected function isAlwaysDark(): bool
    {
        return false;
    }

    protected function hasThemes(): bool
    {
        return true;
    }
}
