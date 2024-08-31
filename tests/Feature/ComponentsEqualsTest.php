<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Alert;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Boolean;
use MoonShine\UI\Components\Card;
use MoonShine\UI\Components\Carousel;
use MoonShine\UI\Components\Color;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Dropdown;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Components\Files;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Icon;
use MoonShine\UI\Components\Layout\Assets;
use MoonShine\UI\Components\Layout\Block;
use MoonShine\UI\Components\Layout\Body;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Burger;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Content;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Favicon;
use MoonShine\UI\Components\Layout\Flash;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Footer;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Head;
use MoonShine\UI\Components\Layout\Header;
use MoonShine\UI\Components\Layout\Html;
use MoonShine\UI\Components\Layout\Layout;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Layout\Logo;
use MoonShine\UI\Components\Layout\Menu;
use MoonShine\UI\Components\Layout\Meta;
use MoonShine\UI\Components\Layout\MobileBar;
use MoonShine\UI\Components\Layout\Sidebar;
use MoonShine\UI\Components\Layout\ThemeSwitcher;
use MoonShine\UI\Components\Layout\TopBar;
use MoonShine\UI\Components\Layout\Wrapper;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Components\Metrics\Wrapped\DonutChartMetric;
use MoonShine\UI\Components\Metrics\Wrapped\LineChartMetric;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Components\OffCanvas;
use MoonShine\UI\Components\Popover;
use MoonShine\UI\Components\ProgressBar;
use MoonShine\UI\Components\Rating;
use MoonShine\UI\Components\Spinner;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Thumbnails;
use MoonShine\UI\Components\Title;
use MoonShine\UI\Components\Url;
use MoonShine\UI\Components\When;

uses()->group('components');

beforeEach(function () {
    $this->components = [
        FlexibleRender::make('__SLOT'),
        Box::make(),
        Box::make(),
        FlexibleRender::make('__END_SLOT'),
    ];

    $this->slot = (string) Components::make($this->components)->render();
});

function compare(
    MoonShineComponent $component,
    array $parameters = [],
    array $attributes = [],
    string $slot = '',
    string $alias = null
): void {
    if ($attributes === []) {
        $attributes = [
            'class' => 'test-class',
            'data-test' => 'test',
        ];

        $component->customAttributes($attributes);
    }

    $html = (string) value($component->render());
    $alias = $alias ?? str_replace(['components.'], [''], $component->getView());

    $bladeHtml = renderBlade($alias, $parameters, $attributes, $slot);

    if (str_contains($bladeHtml, "\n    __SLOT")) {
        $bladeHtml = preg_replace("/\n\s*__SLOT/", "\n    __SLOT", $bladeHtml);
        $html = preg_replace("/__END_SLOT\s*/", "__END_SLOT\n", $html);
    }

    expect($html)->toEqual($bladeHtml);
}

function renderBlade(string $alias, array $parameters = [], array $attributes = [], string $slot = ''): string
{
    $array = static function ($array): string {
        $str = '[';
        foreach ($array as $key => $value) {
            $str .= "'$key' => '$value', ";
        }
        $str = rtrim($str, ', ');
        $str .= ']';

        return $str;
    };

    $params = collect($parameters)->implode(fn ($k, $v) => is_array($k) ? " :$v=\"{$array($k)}\"" : " $v='$k' ");
    $attr = collect($attributes)->implode(fn ($k, $v) => " $v='$k' ");

    return Blade::render("<x-$alias $params $attr>$slot</x-$alias>");
}

describe('Layouts', function () {
    it('assets', function () {
        compare(Assets::make());
    });

    it('block', function () {
        compare(Block::make($this->components), slot: $this->slot);
    });

    it('body', function () {
        compare(Body::make());
    });

    it('box', function () {
        compare(
            Box::make('Label', $this->components)->dark(),
            [':dark' => true, 'title' => 'Label'],
            slot: $this->slot
        );
    });

    it('burger', function () {
        compare(Burger::make());
    });

    it('column', function () {
        compare(Column::make($this->components)->columnSpan(6, 6), [
            'col-span' => 6,
            'adaptive-col-span' => 6,
        ], slot: $this->slot);
    });

    it('content', function () {
        compare(Content::make($this->components), slot: $this->slot);
    });

    it('div', function () {
        compare(Div::make($this->components), slot: $this->slot);
    });

    it('divider', function () {
        compare(Divider::make('Title')->centered(), [
            'label' => 'Title',
            'is-centered' => true,
        ]);
    });

    it('favicon', function () {
        compare(
            Favicon::make()->assets(['test.js'])->bodyColor('#fff'),
            ['custom-assets' => ['test.js'], 'body-color' => '#fff']
        );
    });

    it('flash', function () {
        compare(
            Flash::make(key: 'flash', type: 'info', withToast: false, removable: false),
            ['key' => 'flash', 'type' => 'info', 'withToast' => false, 'removable' => false]
        );
    });

    it('flex', function () {
        compare(
            Flex::make($this->components)
                ->columnSpan(6, 6)
                ->withoutSpace()
                ->itemsAlign('center')
                ->justifyAlign('start'),
            [
                'without-space' => true,
                'items-align' => 'center',
                'justify-align' => 'start',
                'col-span' => 6,
                'adaptive-col-span' => 6,
            ],
            slot: $this->slot
        );
    });

    it('footer', function () {
        compare(
            Footer::make()->menu(['/' => '#'])->copyright('2020'),
            ['menu' => ['/' => '#'], 'copyright' => '2020'],
        );
    });

    it('grid', function () {
        compare(
            Grid::make(),
        );
    });

    it('head', function () {
        compare(
            Head::make()->title('Title')->bodyColor('#fff'),
            ['title' => 'Title', 'body-color' => '#fff'],
        );
    });

    it('header', function () {
        compare(
            Header::make($this->components),
            slot: $this->slot
        );
    });

    it('html', function () {
        compare(
            Html::make()->withThemes(),
            ['with-themes' => true],
        );
    });

    it('layout', function () {
        compare(
            Layout::make()->bodyClass('#fff'),
            ['body-class' => '#fff'],
        );
    });

    it('line-break', function () {
        compare(
            LineBreak::make(),
        );
    });

    it('logo', function () {
        compare(
            Logo::make(href: '/', logo: 'logo.png', logoSmall: 'logo-small.png', title: 'Title')->minimized(),
            [
                'href' => '/',
                'logo' => 'logo.png',
                'logo-small' => 'logo-small.png',
                'title' => 'Title',
                'minimized' => true,
            ]
        );
    });

    it('menu', function () {
        compare(
            Menu::make()->top()->scrollTo(),
            ['menu-manager' => moonshineMenu(), 'top' => true, 'scroll-to' => true]
        );
    })->skip('only primitive can pass in tests');

    it('meta', function () {
        compare(
            Meta::make()->customAttributes([
                'name' => 'csrf-token',
                'content' => 'token',
            ]),
            attributes: ['name' => 'csrf-token', 'content' => 'token'],
            alias: 'moonshine::layout.meta'
        );
    });

    it('mobile-bar', function () {
        compare(
            MobileBar::make($this->components),
            slot: $this->slot,
        );
    });

    it('sidebar', function () {
        compare(
            Sidebar::make()->collapsed(),
            ['collapsed' => true],
        );
    });

    it('theme-switcher', function () {
        compare(
            ThemeSwitcher::make()->top(),
            ['top' => true]
        );
    });

    it('top-bar', function () {
        compare(
            TopBar::make($this->components),
            slot: $this->slot,
        );
    });

    it('wrapper', function () {
        compare(
            Wrapper::make($this->components),
            slot: $this->slot
        );
    });
});

describe('Metrics', function () {
    it('line', function () {
        compare(
            LineChartMetric::make('Title'),
            ['label' => 'Title'],
            ['::id' => "\$id(`metrics`)"]
        );
    });

    it('value', function () {
        compare(
            ValueMetric::make('Title'),
            ['label' => 'Title'],
            ['::id' => "\$id(`metrics`)"]
        );
    });

    it('donut', function () {
        compare(
            DonutChartMetric::make('Title'),
            ['label' => 'Title'],
            ['::id' => "\$id(`metrics`)"]
        );
    });
});

describe('Basic', function () {
    it('action-group', function () {
        compare(
            ActionGroup::make(),
        );
    });

    it('alert', function () {
        compare(
            Alert::make('users', 'info', true)->content('Text'),
            ['icon' => 'users', 'type' => 'info', 'removable' => true],
            slot: 'Text'
        );
    });

    it('badge', function () {
        compare(
            Badge::make('Badge', 'red'),
            ['color' => 'red'],
            slot: 'Badge'
        );
    });

    it('boolean', function () {
        compare(
            Boolean::make(true),
            ['value' => true],
        );
    });

    it('card', function () {
        compare(
            Card::make(title: 'Title')
                ->url('/')
                ->thumbnail('image.png')
                ->subtitle('Subtitle')
                ->values(['Key' => 'Value'])
                ->overlay(),
            [
                'title' => 'Title',
                'subtitle' => 'Subtitle',
                'thumbnail' => 'image.png',
                'url' => '/',
                'values' => ['Key' => 'Value'],
                'overlay' => true,
            ]
        );
    });

    it('carousel', function () {
        compare(
            Carousel::make(['image1.png', 'image2.png'], portrait: true, alt: 'Alt'),
            ['items' => ['image1.png', 'image2.png'], 'portrait' => true, 'alt' => 'Alt'],
        );
    });

    it('color', function () {
        compare(
            Color::make('red'),
            ['color' => 'red'],
        );
    });

    it('components', function () {
        compare(
            Components::make(),
        );
    });

    it('fields-group', function () {
        compare(
            FieldsGroup::make(),
        );
    });

    it('dropdown', function () {
        compare(
            Dropdown::make('Title')
                ->items(['Content 1', 'Content 2'])
                ->searchable()
                ->toggler('Open')
                ->content('Slot')
                ->searchPlaceholder('Placeholder')
                ->footer('Footer')
                ->placement('bottom-start'),
            [
                'title' => 'Title',
                'items' => ['Content 1', 'Content 2'],
                'searchable' => true,
                'toggler' => 'Open',
                'footer' => 'Footer',
                'searchPlaceholder' => 'Placeholder',
                'placement' => 'bottom-start',
            ],
            slot: 'Slot',
        );
    });

    it('files', function () {
        compare(
            Files::make(['file.pdf', 'file2.pdf'], download: true),
            ['files' => ['file.pdf', 'file2.pdf'], 'download' => true]
        );
    });

    it('flexible-render', function () {
        compare(
            FlexibleRender::make('Content'),
            ['content' => 'Content']
        );
    });

    it('heading', function () {
        compare(
            Heading::make('Title')->h(2, false),
            ['label' => 'Title', 'h' => 2, 'as-class' => false]
        );
    });

    it('icon', function () {
        compare(
            Icon::make(icon: 'users', size: 5, color: 'red'),
            ['icon' => 'users', 'size' => 5, 'color' => 'red']
        );
    });

    it('link', function () {
        compare(
            Link::make(href: '/', label: 'Label')->class('test'),
            ['href' => '/'],
            ['class' => 'test'],
            slot: 'Label'
        );
    });

    it('modal', function () {
        compare(
            Modal::make('Title'),
            ['title' => 'Title'],
        );
    });

    it('off-canvas', function () {
        compare(
            OffCanvas::make('Title'),
            ['title' => 'Title'],
        );
    });

    it('popover', function () {
        compare(
            Popover::make('Title', trigger: 'Trigger', placement: 'right'),
            ['title' => 'Title', 'trigger' => 'Trigger', 'placement' => 'right'],
        );
    });

    it('progress-bar', function () {
        compare(
            ProgressBar::make(80, 'sm', 'red')->radial(),
            ['value' => 80, 'size' => 'sm', 'color' => 'red', 'radial' => true],
        );
    })->skip('\n before value');

    it('rating', function () {
        compare(
            Rating::make(3, min: 5, max: 1),
            ['value' => 3, 'min' => 5, 'max' => 1],
        );
    });

    it('spinner', function () {
        compare(
            Spinner::make(size: 'sm', color: 'red')->fixed()->absolute(),
            ['size' => 'sm', 'color' => 'red', 'fixed' => true, 'absolute' => true],
        );
    });

    it('tabs', function () {
        compare(
            Tabs::make([
                Tab::make('Tab 1', [
                    FlexibleRender::make('Content 1'),
                ]),
            ]),
            ['items' => ['Tab 1' => 'Content 1']],
        );
    })->skip('unique ids different');

    it('thumbnails', function () {
        compare(
            Thumbnails::make(['image.png', 'image2.png']),
            ['items' => ['image.png', 'image2.png']],
        );

        compare(
            Thumbnails::make('image.png'),
            ['items' => 'image.png'],
        );
    });

    it('title', function () {
        compare(
            Title::make('Title'),
            slot: 'Title'
        );
    });

    it('url', function () {
        compare(
            Url::make('/', 'Link', icon: 'users', blank: true)->withoutIcon(),
            ['href' => '/', 'value' => 'Link', 'icon' => 'users', 'blank' => true, 'without-icon' => true]
        );
    });

    it('when', function () {
        compare(
            When::make(fn () => false, fn () => $this->components),
        );
    });
});

describe('Form', function () {
    it('form', function () {
        $blade = renderBlade('moonshine::form', attributes: ['class' => 'test-class']);

        expect($blade)->toContain('<form', 'test-class');
    });

    it('input', function () {
        $blade = renderBlade('moonshine::form.input', attributes: ['class' => 'test-class']);

        expect($blade)->toContain('<input', 'test-class');
    });

    it('button', function () {
        $blade = renderBlade('moonshine::form.button', attributes: ['class' => 'test-class']);

        expect($blade)->toContain('<button', 'test-class');
    });

    it('file', function () {
        $blade = renderBlade('moonshine::form.file', attributes: ['class' => 'test-class']);

        expect($blade)->toContain('<input', 'test-class', 'type="file"');
    });

    it('file-item', function () {
        $blade = renderBlade('moonshine::form.file-item', ['file' => 'image.jpg']);

        expect($blade)->toContain('<input', 'image.jpg', '<img', 'type="hidden"');
    });

    it('hint', function () {
        $blade = renderBlade('moonshine::form.hint', attributes: ['class' => 'test-class'], slot: 'Content');

        expect($blade)->toContain('form-hint', 'test-class', 'Content');
    });

    it('input-error', function () {
        $blade = renderBlade('moonshine::form.input-error', attributes: ['class' => 'test-class'], slot: 'Content');

        expect($blade)->toContain('form-error', 'test-class', 'Content');
    });

    it('input-wrapper', function () {
        $blade = renderBlade('moonshine::form.input-wrapper', attributes: ['class' => 'test-class'], slot: 'Content');

        expect($blade)->toContain('form-group moonshine-field', 'test-class', 'Content');
    });

    it('label', function () {
        $blade = renderBlade('moonshine::form.label', attributes: ['class' => 'test-class'], slot: 'Content');

        expect($blade)->toContain('<label', 'test-class', 'Content');
    });

    it('select', function () {
        $blade = renderBlade('moonshine::form.select', ['options' => 'option 1 option 2'], attributes: ['class' => 'test-class'], slot: 'Content');

        expect($blade)->toContain('<select', 'test-class', 'option 1', 'option 2');
    });

    it('slide-range', function () {
        $blade = renderBlade('moonshine::form.slide-range', ['fromName' => 'from', 'toName' => 'to', 'fromValue' => 0, 'toValue' => 100], attributes: ['class' => 'test-class']);

        expect($blade)->toContain('form-group-range', 'test-class');
    });

    it('switcher', function () {
        $blade = renderBlade('moonshine::form.switcher', attributes: ['class' => 'test-class']);

        expect($blade)->toContain('form-switcher-toggler', 'test-class');
    });

    it('textarea', function () {
        $blade = renderBlade('moonshine::form.textarea', attributes: ['class' => 'test-class']);

        expect($blade)->toContain('<textarea', 'test-class');
    });
});

describe('Table', function () {
    it('table', function () {
        $blade = renderBlade('moonshine::table', ['tbody' => 'Title'], attributes: ['class' => 'test-class']);

        expect($blade)->toContain('<table', 'test-class', 'Title');
    });
});
