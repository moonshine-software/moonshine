<?php

declare(strict_types=1);

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Core\Collections\Components as ComponentsCollection;
use MoonShine\Laravel\Layouts\BaseLayout;
use MoonShine\Laravel\Pages\Dashboard;
use MoonShine\Tests\Fixtures\Pages\ContentSpacingAsyncPage;
use MoonShine\Tests\Fixtures\Pages\ContentSpacingTestPage;
use MoonShine\Tests\Fixtures\Pages\ContentSpacingTestPageWithTrait;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Table\TableBuilder;

uses()->group('components');

afterEach(function (): void {
    $this->moonshineCore->getConfig()->contentSpacing(false);
});

function makeSpacingTestPage(object $core, bool $withTrait = false): Dashboard
{
    $class = $withTrait ? ContentSpacingTestPageWithTrait::class : ContentSpacingTestPage::class;

    return new $class($core);
}

function layoutBuiltComponents(Dashboard $page): ComponentsCollection
{
    return ComponentsCollection::make([
        $page->getLayout()->build(),
    ]);
}

function layoutContentSpacingWrappers(Dashboard $page): ComponentsCollection
{
    return layoutBuiltComponents($page)
        ->onlyComponents()
        ->filter(
            static fn (ComponentContract $component): bool => $component->getName() === BaseLayout::CONTENT_SPACING_NAME
        );
}

function layoutContentFragmentHtml(Dashboard $page): string
{
    $fragment = layoutBuiltComponents($page)->findByName(BaseLayout::CONTENT_FRAGMENT_NAME);

    if (! $fragment instanceof ComponentContract) {
        return '';
    }

    return (string) $fragment->render();
}

it('keeps content spacing off by default', function (): void {
    expect($this->moonshineCore->getConfig()->isContentSpacing())->toBeFalse();

    $page = makeSpacingTestPage($this->moonshineCore);
    $components = $page->getComponents();

    expect($components)->toHaveCount(2)
        ->and($components->first())->not->toBeInstanceOf(Div::class);
});

it('wraps page components with the WithContentSpacing trait', function (): void {
    $page = makeSpacingTestPage($this->moonshineCore, withTrait: true);
    $components = $page->getComponents();

    expect($components)->toHaveCount(1);

    $root = $components->first();

    expect($root)->toBeInstanceOf(Div::class)
        ->and($root->getName())->toBe(BaseLayout::CONTENT_SPACING_NAME)
        ->and((string) $root->getAttribute('class'))->toContain('space-elements');
});

it('does not let the content spacing wrapper shadow unnamed child components', function (): void {
    createItem(2);

    $page = app(ContentSpacingAsyncPage::class);

    $component = $page->getComponents()->findByName('default');

    expect($component)->toBeInstanceOf(TableBuilder::class);
});

it('reloads only the unnamed async table via the component endpoint when using the trait', function (): void {
    $item = createItem(2);

    $page = app(ContentSpacingAsyncPage::class);
    $uriKey = $page->getUriKey();

    $this->moonshineCore->pages([
        ...$this->moonshineCore->getConfig()->getPages(),
        ContentSpacingAsyncPage::class,
    ]);

    asAdmin()->get($this->moonshineCore->getRouter()->to('component', [
        '_component_name' => 'default',
        'pageUri' => $uriKey,
    ]))
        ->assertSee($item->name)
        ->assertSee('js-table-builder-container', escape: false)
        ->assertDontSee(ContentSpacingAsyncPage::NEIGHBOR_MARKER)
        ->assertDontSee('Neighbor content')
        ->assertOk();
});

it('adds a single layout spacing wrapper when the config flag is enabled', function (): void {
    $this->moonshineCore->getConfig()->contentSpacing();

    $page = makeSpacingTestPage($this->moonshineCore);

    expect(layoutContentSpacingWrappers($page))->toHaveCount(1)
        ->and(layoutContentSpacingWrappers($page)->first())->toBeInstanceOf(Div::class);
});

it('wraps only page components when the config flag is enabled, leaving title and subtitle outside', function (): void {
    $this->moonshineCore->getConfig()->contentSpacing();

    $page = makeSpacingTestPage($this->moonshineCore);
    $html = layoutContentFragmentHtml($page);
    $wrapperPos = strpos($html, 'class="space-elements"');
    $titlePos = strpos($html, 'Page title');
    $subtitlePos = strpos($html, 'Page subtitle');

    expect($wrapperPos)->not->toBeFalse()
        ->and($titlePos)->not->toBeFalse()
        ->and($subtitlePos)->not->toBeFalse()
        ->and($titlePos)->toBeLessThan($wrapperPos)
        ->and($subtitlePos)->toBeLessThan($wrapperPos)
        ->and($html)->toContain('toolbar')
        ->and($html)->toContain('Notice');
});

it('does not wrap page components in the layout when the config flag is off', function (): void {
    $page = makeSpacingTestPage($this->moonshineCore);
    $html = layoutContentFragmentHtml($page);

    expect($html)->toContain('Page title')
        ->and($html)->toContain('Page subtitle')
        ->and(layoutContentSpacingWrappers($page))->toHaveCount(0)
        ->and($html)->not->toContain('class="space-elements"');
});

it('does not double-wrap when the trait and the config flag are both enabled', function (): void {
    $this->moonshineCore->getConfig()->contentSpacing();

    $page = makeSpacingTestPage($this->moonshineCore, withTrait: true);

    expect($page->getComponents())->toHaveCount(1)
        ->and(layoutContentSpacingWrappers($page))->toHaveCount(1)
        ->and($page->getComponents()->first()?->getName())->toBe(BaseLayout::CONTENT_SPACING_NAME);
});

it('does not wrap getComponents when only the config flag is enabled', function (): void {
    $this->moonshineCore->getConfig()->contentSpacing();

    $page = makeSpacingTestPage($this->moonshineCore);
    $components = $page->getComponents();

    expect($components)->toHaveCount(2)
        ->and($components->first())->not->toBeInstanceOf(Div::class)
        ->and(layoutContentSpacingWrappers($page))->toHaveCount(1);
});
