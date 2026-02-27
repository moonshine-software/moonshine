<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Dropdown;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Popover;
use MoonShine\UI\Components\Tabs\Tab;

test('phase3 raw flags are false by default and true via html methods', function () {
    $tab = Tab::make('Label')
        ->labelHtml('<strong>Label</strong>');

    $badge = Badge::make('Value')
        ->valueHtml('<strong>Value</strong>');

    $dropdown = Dropdown::make()
        ->titleHtml('<strong>Title</strong>')
        ->itemsHtml(['<em>item</em>'])
        ->footerHtml('<small>Footer</small>');

    $popover = Popover::make('Title')
        ->triggerHtml('<strong>Trigger</strong>');

    $metric = ValueMetric::make('Title')
        ->labelHtml('<strong>Title</strong>')
        ->valueFormatHtml('<strong>{value}</strong>');

    expect(Tab::make('Label')->isLabelRaw())->toBeFalse()
        ->and($tab->isLabelRaw())->toBeTrue()
        ->and(Badge::make('Value')->isValueRaw())->toBeFalse()
        ->and($badge->isValueRaw())->toBeTrue()
        ->and(Dropdown::make()->isTitleRaw())->toBeFalse()
        ->and(Dropdown::make()->isItemsRaw())->toBeFalse()
        ->and(Dropdown::make()->isFooterRaw())->toBeFalse()
        ->and($dropdown->isTitleRaw())->toBeTrue()
        ->and($dropdown->isItemsRaw())->toBeTrue()
        ->and($dropdown->isFooterRaw())->toBeTrue()
        ->and(Popover::make('Title')->isTriggerRaw())->toBeFalse()
        ->and($popover->isTriggerRaw())->toBeTrue()
        ->and(ValueMetric::make('Title')->isLabelRaw())->toBeFalse()
        ->and($metric->isLabelRaw())->toBeTrue()
        ->and(ValueMetric::make('Title')->isValueRaw())->toBeFalse()
        ->and($metric->isValueRaw())->toBeTrue();
});

test('pagination escapes translates and labels', function () {
    $translates = [
        'previous' => '<script>alert(1)</script>',
        'next' => 'Next &raquo;',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'results' => 'results',
    ];

    $html = Blade::render(
        '<x-moonshine::pagination :simple="true" :prev_page_url="\'/prev\'" :next_page_url="\'/next\'" :translates="$translates" />',
        ['translates' => $translates]
    );

    expect($html)
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->not->toContain('<script>alert(1)</script>')
        ->toContain('Next »');

    $htmlWithLinks = Blade::render(
        '<x-moonshine::pagination :has_pages="true" :current_page="2" :last_page="3" :prev_page_url="\'/prev\'" :next_page_url="\'/next\'" :links="$links" :translates="$translates" />',
        [
            'links' => [
                ['url' => '/1', 'label' => '<img src=x onerror=alert(1)>', 'active' => false],
            ],
            'translates' => $translates,
        ]
    );

    expect($htmlWithLinks)
        ->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->not->toContain('<img src=x onerror=alert(1)>');
});

test('menu item button remains renderable and label is escaped', function () {
    $html = (string) value(MenuItem::make('/users', '<script>alert(1)</script>')->render());

    expect($html)
        ->toContain('menu-link')
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->not->toContain('<script>alert(1)</script>');
});
