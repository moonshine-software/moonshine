<?php

declare(strict_types=1);

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\UI\Fields\Text;

uses()->group('components');

function setLabelEscaping(bool $value): void
{
    config()->set('moonshine.html_escaping.labels', $value);
    app(CoreContract::class)->getConfig()->set('html_escaping.labels', $value);
}

beforeEach(function (): void {
    setLabelEscaping(false);
});

it('config html_escaping.labels defaults to false', function (): void {
    expect(config('moonshine.html_escaping.labels'))->toBeFalse();
});

it('renders raw HTML in label by default (backward compatible)', function (): void {
    expect((string) Text::make('<b>Label</b>')->render())
        ->toContain('<b>Label</b>');
});

it('escapes label HTML when html_escaping.labels is enabled', function (): void {
    setLabelEscaping(true);

    $html = (string) Text::make('<b>Label</b>')->render();

    // The label element should contain the escaped version
    expect($html)->toContain('&lt;b&gt;Label&lt;/b&gt;');
});

it('unescapeLabel(true) renders trusted raw HTML when config escaping is enabled', function (): void {
    setLabelEscaping(true);

    expect((string) Text::make('<b>Label</b>')->unescapeLabel()->render())
        ->toContain('<b>Label</b>');
});

it('unescapeLabel(false) follows global escaping behavior', function (): void {
    setLabelEscaping(true);

    expect((string) Text::make('<b>Label</b>')->unescapeLabel(false)->render())
        ->toContain('&lt;b&gt;Label&lt;/b&gt;');
});

it('unescapeLabel() defaults to true and can be toggled off', function (): void {
    $field = Text::make('Name');
    $field->unescapeLabel()->unescapeLabel(false);

    expect($field->isUnescapeLabel())->toBeFalse();
});

it('unescapeLabel() defaults to false and can be enabled', function (): void {
    $field = Text::make('Name');
    $otherField = Text::make('Name')->unescapeLabel();

    expect($field->isUnescapeLabel())->toBeFalse()
        ->and($otherField->isUnescapeLabel())->toBeTrue();
});
