<?php

declare(strict_types=1);

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Card;
use MoonShine\UI\Components\Dropdown;
use MoonShine\UI\Components\Layout\Menu;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Popover;
use MoonShine\UI\Fields\Text;

uses()->group('components');

function setEscaping(string $key, bool $value): void
{
    config()->set("moonshine.html_escaping.$key", $value);
    app(CoreContract::class)->getConfig()->set("html_escaping.$key", $value);
}

beforeEach(function (): void {
    setEscaping('hints', false);
    setEscaping('labels', false);
    setEscaping('card_values', false);
    setEscaping('ui_elements', false);
});

it('escapes hints only when hints config is enabled', function (): void {
    expect((string) Text::make('Name')->hint('<b>Hint</b>')->render())
        ->toContain('<b>Hint</b>');

    setEscaping('hints', true);

    expect((string) Text::make('Name')->hint('<b>Hint</b>')->render())
        ->toContain('&lt;b&gt;Hint&lt;/b&gt;')
        ->and((string) Text::make('Name')->hintHtml('<b>Hint</b>')->render())
        ->toContain('<b>Hint</b>');
});

it('escapes labels only when labels config is enabled', function (): void {
    expect((string) Text::make('<b>Label</b>')->render())
        ->toContain('<b>Label</b>');

    setEscaping('labels', true);

    expect((string) Text::make('<b>Label</b>')->render())
        ->toContain('&lt;b&gt;label&lt;/b&gt;')
        ->and((string) Text::make('Label')->labelHtml('<b>Label</b>')->render())
        ->toContain('<b>Label</b>');
});

it('escapes card values only when card_values config is enabled', function (): void {
    expect((string) Card::make()->values(['x' => '<b>Value</b>'])->render())
        ->toContain('<b>Value</b>');

    setEscaping('card_values', true);

    expect((string) Card::make()->values(['x' => '<b>Value</b>'])->render())
        ->toContain('&lt;b&gt;Value&lt;/b&gt;')
        ->and((string) Card::make()->valuesHtml(['x' => '<b>Value</b>'])->render())
        ->toContain('<b>Value</b>');
});

it('escapes Badge when ui_elements config is enabled', function (): void {
    setEscaping('ui_elements', true);

    expect((string) Badge::make('<b>Badge</b>', 'red')->render())
        ->toContain('&lt;b&gt;Badge&lt;/b&gt;')
        ->and((string) Badge::make('Badge', 'red')->valueHtml('<b>Badge</b>')->render())
        ->toContain('<b>Badge</b>');
});

it('escapes Dropdown when ui_elements config is enabled', function (): void {
    setEscaping('ui_elements', true);

    expect((string) Dropdown::make('<b>Title</b>', footer: '<b>Footer</b>')->items(['<b>Item</b>'])->render())
        ->toContain('&lt;b&gt;Title&lt;/b&gt;', '&lt;b&gt;Item&lt;/b&gt;', '&lt;b&gt;Footer&lt;/b&gt;')
        ->and((string) Dropdown::make('Title', footer: 'Footer')
            ->titleHtml('<b>Title</b>')
            ->itemsHtml(['<b>Item</b>'])
            ->footerHtml('<b>Footer</b>')
            ->render())
        ->toContain('<b>Title</b>', '<b>Item</b>', '<b>Footer</b>');
});

it('escapes ActionButton when ui_elements config is enabled', function (): void {
    setEscaping('ui_elements', true);

    expect((string) ActionButton::make('<b>Action</b>')->render())
        ->toContain('&lt;b&gt;Action&lt;/b&gt;')
        ->and((string) ActionButton::make('Action')->labelHtml('<b>Action</b>')->render())
        ->toContain('<b>Action</b>');
});

it('escapes MenuItem when ui_elements config is enabled', function (): void {
    setEscaping('ui_elements', true);

    expect((string) Menu::make([
        MenuItem::make('/', '<b>Menu</b>'),
    ])->render())
        ->toContain('&lt;b&gt;Menu&lt;/b&gt;')
        ->and((string) Menu::make([
            MenuItem::make('/', 'Menu')->labelHtml('<b>Menu</b>'),
        ])->render())
        ->toContain('<b>Menu</b>');
});

it('escapes ValueMetric when ui_elements config is enabled', function (): void {
    setEscaping('ui_elements', true);

    expect((string) ValueMetric::make('<b>Metric</b>')->value(10)->valueFormat('<i>{value}</i>')->render())
        ->toContain('&lt;b&gt;Metric&lt;/b&gt;', '&lt;i&gt;10&lt;/i&gt;')
        ->and((string) ValueMetric::make('Metric')
            ->labelHtml('<b>Metric</b>')
            ->value(10)
            ->valueFormatHtml('<i>{value}</i>')
            ->render())
        ->toContain('<b>Metric</b>', '<i>10</i>');
});

it('Popover title is always safe in attribute context', function (): void {
    $html = Popover::make('<script>xss</script>', 'trigger')->render();

    expect((string) $html)->toContain('&lt;script&gt;xss&lt;/script&gt;');
});

it('Popover triggerHtml renders raw trigger content', function (): void {
    setEscaping('ui_elements', true);

    expect((string) Popover::make('Title', '<b>Trigger</b>')->render())
        ->toContain('&lt;b&gt;Trigger&lt;/b&gt;')
        ->and((string) Popover::make('Title', 'Trigger')->triggerHtml('<b>Trigger</b>')->render())
        ->toContain('<b>Trigger</b>');
});
