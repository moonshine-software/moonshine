<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\AssetManager\Css;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Laravel\Components\Layout\{Flash, Locales, Notifications, Profile, Search};
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
    Layout\TopBar,
    Layout\Wrapper,
    When};
use MoonShine\Laravel\Components\Fragment;

class CompactLayout extends AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),

            Css::make('/vendor/moonshine/assets/minimalistic.css')->defer(),
        ];
    }

    protected function colors(ColorManager $colorManager): void
    {
        $colorManager
            ->primary('#1E96FC')
            ->secondary('#1D8A99')
            ->body('255, 255, 255')
            ->dark('30, 31, 67', 'DEFAULT')
            ->dark('249, 250, 251', 50)
            ->dark('243, 244, 246', 100)
            ->dark('229, 231, 235', 200)
            ->dark('209, 213, 219', 300)
            ->dark('156, 163, 175', 400)
            ->dark('107, 114, 128', 500)
            ->dark('75, 85, 99', 600)
            ->dark('55, 65, 81', 700)
            ->dark('31, 41, 55', 800)
            ->dark('17, 24, 39', 900)
            ->successBg('209, 255, 209')
            ->successText('15, 99, 15')
            ->warningBg('255, 246, 207')
            ->warningText('92, 77, 6')
            ->errorBg('255, 224, 224')
            ->errorText('81, 20, 20')
            ->infoBg('196, 224, 255')
            ->infoText('34, 65, 124');

        $colorManager
            ->body('27, 37, 59', dark: true)
            ->dark('83, 103, 132', 50, dark: true)
            ->dark('74, 90, 121', 100, dark: true)
            ->dark('65, 81, 114', 200, dark: true)
            ->dark('53, 69, 103', 300, dark: true)
            ->dark('48, 61, 93', 400, dark: true)
            ->dark('41, 53, 82', 500, dark: true)
            ->dark('40, 51, 78', 600, dark: true)
            ->dark('39, 45, 69', 700, dark: true)
            ->dark('27, 37, 59', 800, dark: true)
            ->dark('15, 23, 42', 900, dark: true)
            ->successBg('17, 157, 17', dark: true)
            ->successText('178, 255, 178', dark: true)
            ->warningBg('225, 169, 0', dark: true)
            ->warningText('255, 255, 199', dark: true)
            ->errorBg('190, 10, 10', dark: true)
            ->errorText('255, 197, 197', dark: true)
            ->infoBg('38, 93, 205', dark: true)
            ->infoText('179, 220, 255', dark: true);
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
                ]),
                Body::make([
                    Wrapper::make([
                        TopBar::make([
                            Block::make([
                                Logo::make(
                                    moonshineRouter()->getEndpoints()->home(),
                                    $logo,
                                    $logoSmall
                                )->minimized(),
                            ])->class('menu-logo'),

                            Block::make([
                                Menu::make()->top(),
                            ])->class('menu-navigation'),

                            Block::make([
                                When::make(
                                    static fn (): bool => moonshineConfig()->isAuthEnabled(),
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
                        ]),

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
                            Fragment::make([
                                Flash::make(),
                                Header::make([
                                    Breadcrumbs::make($this->getPage()->getBreadcrumbs())
                                        ->prepend(moonshineRouter()->getEndpoints()->home(), icon: 'home'),

                                    Search::make(),

                                    Notifications::make(),

                                    Locales::make(),
                                ]),

                                Content::make([
                                    Components::make(
                                        $this->getPage()->getComponents()
                                    ),
                                ]),
                            ])->name('_content')->customAttributes(['id' => 'content']),

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
                ])->class('theme-minimalistic'),
            ])
                ->customAttributes([
                    'lang' => str_replace('_', '-', app()->getLocale()),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
