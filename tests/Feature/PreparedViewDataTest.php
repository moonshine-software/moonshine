<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Text;

uses()->group('components');

it('keeps Blade tag attributes on an icon', function (): void {
    $html = Blade::render('<x-moonshine::icon icon="users" size="4" x-show="open" class="extra" />');

    expect($html)
        ->toContain('class="icon-wrapper size-4 text-current extra"')
        ->toContain('x-show="open"')
        ->toContain('<svg');
});

it('keeps Blade tag attributes on a rating', function (): void {
    $html = Blade::render('<x-moonshine::rating :value="2" data-test="rating" />');

    expect($html)
        ->toContain('class="flex items-center gap-x-1"')
        ->toContain('data-test="rating"')
        ->and(substr_count($html, 'text-primary'))
        ->toBe(2)
        ->and(substr_count($html, 'text-secondary'))
        ->toBe(3);
});

it('renders an action button link without nested Blade components', function (): void {
    $html = (string) ActionButton::make('Open', '/items?page=1&sort=id')
        ->customAttributes(['data-test' => 'button'])
        ->badge(3);

    expect($html)
        ->toContain('<a class="btn" href="/items?page=1&amp;sort=id" data-test="button">')
        ->toContain('Open')
        ->toContain('<span class="badge">3</span>');
});

it('renders an action button with a type as a button', function (): void {
    $html = (string) ActionButton::make('Save')->customAttributes(['type' => 'submit']);

    expect($html)
        ->toContain('<button type="submit" class="btn">')
        ->not->toContain('href=');
});

it('keeps Blade tag attributes on an action button', function (): void {
    $html = Blade::render('<x-moonshine::action-button label="Open" url="/items" data-test="button" />');

    expect($html)->toContain('<a class="btn" href="/items" data-test="button">');
});

it('adds the csrf token only to non-get forms', function (): void {
    $post = (string) FormBuilder::make('/save', FormMethod::POST, [Text::make('Title')]);
    $get = (string) FormBuilder::make('/search', FormMethod::GET, [Text::make('Title')]);

    expect($post)
        ->toContain('name="_token"')
        ->toContain('x-id="[\'form\']"')
        ->toContain('class="form space-elements"')
        ->and($get)
        ->not->toContain('name="_token"');
});

it('renders a hidden field input directly', function (): void {
    $html = (string) Hidden::make('Token')->setValue('a&b');

    expect($html)
        ->toContain('class="form-hidden"')
        ->toContain('type="hidden"')
        ->toContain('value="a&amp;b"');
});

it('renders the skeleton only for async tables', function (): void {
    $items = [['title' => 'First']];

    $static = (string) TableBuilder::make([Text::make('Title')], $items);
    $async = (string) TableBuilder::make([Text::make('Title')], $items)->async('/table');

    expect($static)
        ->not->toContain('data-skeleton')
        ->and($async)
        ->toContain('data-skeleton');
});
