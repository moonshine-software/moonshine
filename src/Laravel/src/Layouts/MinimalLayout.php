<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\UI\Components\
{Layout\Body, Layout\Content, Layout\Div, Layout\Flash, Layout\Html, Layout\Layout, Layout\Wrapper};

class MinimalLayout extends AppLayout
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    /**
     * @param  ColorManager  $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        $colorManager
            ->primary('0.6632 0.1802 250.61')
            ->secondary('0.5827 0.0938 209.17')
            ->body('0.9846 0.0017 247.84')
            ->dark('0.2596 0.0663 279.52', 'DEFAULT')
            ->dark('0.9846 0.0017 247.84', 50)
            ->dark('0.967 0.0029 264.54', 100)
            ->dark('0.9276 0.0058 264.53', 200)
            ->dark('0.8717 0.0093 258.34', 300)
            ->dark('0.7137 0.0192 261.32', 400)
            ->dark('0.551 0.0234 264.36', 500)
            ->dark('0.4461 0.0263 256.8', 600)
            ->dark('0.3729 0.0306 259.73', 700)
            ->dark('0.2781 0.0296 256.85', 800)
            ->dark('0.2101 0.0318 264.66', 900)
            ->successBg('0.956 0.0772 144.94')
            ->successText('0.4359 0.138 142.75')
            ->warningBg('0.9704 0.0514 96.45')
            ->warningText('0.4232 0.0841 95.58')
            ->errorBg('0.9322 0.0343 17.78')
            ->errorText('0.2953 0.091 24.64')
            ->infoBg('0.8962 0.0522 251.06')
            ->infoText('0.3853 0.1068 261.85');

        $colorManager
            ->body('0.2665 0.0437 264.94', dark: true)
            ->dark('0.5091 0.0523 257.61', 50, dark: true)
            ->dark('0.4671 0.0545 263.27', 100, dark: true)
            ->dark('0.4358 0.0585 264.2', 200, dark: true)
            ->dark('0.3926 0.0616 264.47', 300, dark: true)
            ->dark('0.3638 0.0582 266.67', 400, dark: true)
            ->dark('0.3324 0.0541 266.37', 500, dark: true)
            ->dark('0.3242 0.0506 266.68', 600, dark: true)
            ->dark('0.303 0.0443 272.77', 700, dark: true)
            ->dark('0.2665 0.0437 264.94', 800, dark: true)
            ->dark('0.2077 0.0398 265.75', 900, dark: true)
            ->successBg('0.6048 0.1994 142.61', dark: true)
            ->successText('0.9308 0.1279 144.46', dark: true)
            ->warningBg('0.7661 0.1571 84.56', dark: true)
            ->warningText('0.9865 0.0716 107.64', dark: true)
            ->errorBg('0.5064 0.2034 28.77', dark: true)
            ->errorText('0.8751 0.0665 18.51', dark: true)
            ->infoBg('0.5102 0.1835 262.32', dark: true)
            ->infoText('0.877 0.065 244.38', dark: true);
    }

    protected function withTitle(): bool
    {
        return false;
    }

    protected function withSubTitle(): bool
    {
        return false;
    }

    public function build(): Layout
    {
        return Layout::make([
            Html::make([
                $this->getHeadComponent(),
                Body::make([
                    Wrapper::make([
                        // $this->getTopBarComponent(),
                        $this->getSidebarComponent(),
                        Div::make([
                            Fragment::make([
                                Flash::make(),

                                $this->getHeaderComponent(),

                                Content::make($this->getContentComponents()),

                                $this->getFooterComponent(),
                            ])->class('layout-page')->name(self::CONTENT_FRAGMENT_NAME),
                        ])->class('flex grow overflow-auto')->customAttributes(['id' => self::CONTENT_ID]),
                    ]),
                ])->class('theme-minimalistic'),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes($this->isAlwaysDark()),
        ]);
    }
}
