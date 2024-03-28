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
use MoonShine\Theme\ColorManager;

final class AppLayout extends MoonShineLayout
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

    protected function colors(ColorManager $colorManager): void
    {
        $colorManager
            ->primary('120, 67, 233')
            ->secondary('236, 65, 118')
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
            ->dark('74, 90, 12', 100, dark: true)
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
                            /*Search::make(),
                            Locales::make(),
                            FlexibleRender::make(view('moonshine::layouts.shared.notifications')),
                            Flex::make([
                                FlexibleRender::make(view('moonshine::layouts.shared.logo')),
                                ThemeSwitcher::make(),
                            ]),
                            Divider::make(),*/
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
                                    ->add(moonshineRouter()->home(), '', 'heroicons.outline.home'),

                                Search::make(),
                                FlexibleRender::make(view('moonshine::layouts.shared.notifications')),
                                Locales::make(),
                            ]),

                            Content::make([
                                Title::make($page->title()),

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
                ])->class('theme-minimalistic'),
            ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
