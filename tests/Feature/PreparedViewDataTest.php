<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentSlot;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Icon;
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

it('uses an explicit Blade icon slot instead of the named icon', function (): void {
    $html = Blade::render('<x-moonshine::icon icon="star"><svg id="custom-icon"></svg></x-moonshine::icon>');

    expect($html)->toContain('<svg id="custom-icon"></svg>')->not->toContain('data-slot="icon"');
});

it('keeps custom icon content and empty slot fallback', function (): void {
    expect((string) Icon::make('<svg id="custom-icon"></svg>')->custom())
        ->toContain('<svg id="custom-icon"></svg>')
        ->and(Blade::render('<x-moonshine::icon icon="star"> </x-moonshine::icon>'))
        ->toContain('data-slot="icon"');
});

it('uses overridden icon slots from custom view data', function (mixed $slot): void {
    $html = (string) Icon::make('star')->customView('moonshine::components.icon', ['slot' => $slot]);

    expect($html)->toContain('id="custom-icon"')->not->toContain('data-slot="icon"');
})->with([
    'string' => ['<svg id="custom-icon"></svg>'],
    'html' => [new HtmlString('<svg id="custom-icon"></svg>')],
    'slot' => [new ComponentSlot('<svg id="custom-icon"></svg>')],
]);

it('keeps action button custom view url and raw preferences', function (bool $raw): void {
    $html = (string) ActionButton::make('Open', '/old')
        ->customView('moonshine::components.action-button', ['url' => '/new?a=1&b=2', 'raw' => $raw]);

    expect($html)->toContain('href="/new?a=1&amp;b=2"');
    expect(str_contains($html, 'class="btn"'))->toBe(! $raw);
})->with([true, false]);

it('evaluates an action button url callback once', function (): void {
    $calls = 0;
    $button = ActionButton::make('Open', static function () use (&$calls): string {
        $calls++;

        return '/items';
    });

    expect((string) $button)->toContain('href="/items"')
        ->and($calls)->toBe(1);
});

it('keeps the filled action button style', function (bool $filled, bool $raw): void {
    $button = ActionButton::make('Open', '/items')->customAttributes(['filled' => $filled]);
    if ($raw) {
        $button->rawMode();
    }
    $html = (string) $button;

    expect(str_contains($html, 'btn-primary'))->toBe($filled && ! $raw);
    expect($html)->not->toContain('filled=');
})->with([true, false])->with([true, false]);

it('keeps filled style passed through a Blade tag', function (): void {
    expect(Blade::render('<x-moonshine::action-button label="Open" url="/items" :filled="true" />'))
        ->toContain('btn-primary');
});

it('omits resolved empty badges', function (mixed $badge, bool $button): void {
    $action = ActionButton::make('Open', '/items')->badge($badge);
    if ($button) {
        $action->customAttributes(['type' => 'button']);
    }

    expect((string) $action)->not->toContain('class="badge"');
})->with([
    'false' => [false],
    'null' => [null],
    'false callback' => [static fn (): false => false],
    'null callback' => [static fn (): null => null],
])->with([true, false]);

it('uses a badge supplied through custom view data', function (): void {
    $html = (string) ActionButton::make('Open', '/items')
        ->customView('moonshine::components.action-button', ['badge' => 7]);

    expect($html)->toContain('<span class="badge">7</span>');
});

it('keeps a form raw override from custom view data', function (bool $raw): void {
    $html = (string) FormBuilder::make('/save')->customView('moonshine::components.form.builder', ['raw' => $raw]);

    expect(str_contains($html, 'class="form space-elements"'))->toBe(! $raw);
    expect(str_contains($html, 'grid-cols-12'))->toBe(! $raw);
})->with([true, false]);

it('renders overridden form buttons and errors', function (): void {
    $html = (string) FormBuilder::make('/save')->customView('moonshine::components.form.builder', [
        'buttons' => collect([ActionButton::make('Custom action', '/custom')]),
        'errors' => ['name' => ['Custom error']],
    ]);

    expect($html)->toContain('Custom action')->toContain('Custom error');
});

it('can hide form errors through custom view data', function (): void {
    session()->put('errors', new ViewErrorBag()->put('default', new MessageBag(['name' => ['Hidden error']])));
    $html = (string) FormBuilder::make('/save')->name('default')
        ->customView('moonshine::components.form.builder', ['errorsAbove' => false]);

    expect($html)->not->toContain('Hidden error');
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
