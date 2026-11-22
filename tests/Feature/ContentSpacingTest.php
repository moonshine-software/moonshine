<?php

declare(strict_types=1);

use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Core\Collections\Components as ComponentsCollection;
use MoonShine\Laravel\Layouts\BaseLayout;
use MoonShine\Laravel\Pages\Dashboard;
use MoonShine\Tests\Fixtures\Pages\ContentSpacingAsyncPage;
use MoonShine\Tests\Fixtures\Pages\ContentSpacingTestPage;
use MoonShine\Tests\Fixtures\Pages\ContentSpacingTestPageWithTrait;
use MoonShine\UI\Components\FlexibleRender;
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

it('wraps page components only in the layout with the WithContentSpacing trait', function (): void {
    $page = makeSpacingTestPage($this->moonshineCore, withTrait: true);
    $components = $page->getComponents();
    $wrappers = layoutContentSpacingWrappers($page);

    expect($components)->toHaveCount(2)
        ->and($components->first())->not->toBeInstanceOf(Div::class)
        ->and($page->getComponents())->toBe($components)
        ->and($wrappers)->toHaveCount(1)
        ->and((string) $wrappers->first()->getAttribute('class'))->toContain('space-elements');
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

it('keeps title and subtitle outside the spacing wrapper', function (bool $withTrait): void {
    $this->moonshineCore->getConfig()->contentSpacing(! $withTrait);

    $page = makeSpacingTestPage($this->moonshineCore, withTrait: $withTrait);
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
})->with(['config' => false, 'trait' => true]);

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

    expect($page->getComponents())->toHaveCount(2)
        ->and(layoutContentSpacingWrappers($page))->toHaveCount(1);
});

it('keeps the configurator contract compatible with existing implementations', function (): void {
    expect((new ReflectionClass(ConfiguratorContract::class))->hasMethod('isContentSpacing'))->toBeFalse();
});

it('reads content spacing through the existing configurator get method', function (bool $enabled): void {
    $config = Mockery::mock(ConfiguratorContract::class);
    $config->shouldReceive('get')->with('content_spacing', false)->once()->andReturn($enabled);

    $core = Mockery::mock(CoreContract::class);
    $core->shouldReceive('getConfig')->andReturn($config);

    $page = makeSpacingTestPage($this->moonshineCore);
    $layout = Mockery::mock(BaseLayout::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $layout->shouldReceive('getCore')->andReturn($core);
    $layout->shouldReceive('getPage')->andReturn($page);
    $layout->shouldReceive('withTitle')->andReturn(false);

    $components = (new ReflectionMethod(BaseLayout::class, 'getContentComponents'))->invoke($layout);
    $html = (string) $components[0]->render();

    expect(str_contains($html, 'class="space-elements"'))->toBe($enabled);
})->with([false, true]);

it('preserves component collection identity and mutations with the trait', function (): void {
    $page = makeSpacingTestPage($this->moonshineCore, withTrait: true);
    $components = $page->getComponents();
    $added = FlexibleRender::make('Added after initialization')->name('added');
    $components->push($added);

    expect($page->getComponents())->toBe($components)
        ->and($page->getComponents()->findByName('added'))->toBe($added)
        ->and(layoutContentFragmentHtml($page))->toContain('Added after initialization')
        ->and($page->getComponents())->toBe($components)
        ->and($components)->toHaveCount(3);
});

it('preserves setComponents with the trait', function (): void {
    $page = makeSpacingTestPage($this->moonshineCore, withTrait: true);
    $components = ComponentsCollection::make([FlexibleRender::make('Replacement')]);
    $page->setComponents($components);

    expect($page->getComponents())->toBe($components)
        ->and(layoutContentFragmentHtml($page))->toContain('Replacement')
        ->and(layoutContentSpacingWrappers($page))->toHaveCount(1);
});

it('reloads only the component named _spacing without its siblings', function (): void {
    createItem(2);

    $page = app(ContentSpacingAsyncPage::class);
    $component = $page->getComponents()->findByName('_spacing');

    expect($component)->toBeInstanceOf(FlexibleRender::class);

    $this->moonshineCore->pages([
        ...$this->moonshineCore->getConfig()->getPages(),
        ContentSpacingAsyncPage::class,
    ]);

    asAdmin()->get($this->moonshineCore->getRouter()->to('component', [
        '_component_name' => '_spacing',
        'pageUri' => $page->getUriKey(),
    ]))
        ->assertOk()
        ->assertSee('Named spacing component')
        ->assertDontSee('js-table-builder-container', escape: false)
        ->assertDontSee(ContentSpacingAsyncPage::NEIGHBOR_MARKER)
        ->assertDontSee('Neighbor content');
});

it('does not wrap empty page components', function (bool $withTrait): void {
    $this->moonshineCore->getConfig()->contentSpacing(! $withTrait);
    $page = makeSpacingTestPage($this->moonshineCore, withTrait: $withTrait)->setComponents([]);

    expect($page->getComponents())->toBeEmpty()
        ->and(layoutContentSpacingWrappers($page))->toBeEmpty();
})->with(['config' => false, 'trait' => true]);

it('avoids double wrapping a page that already has a spacing div', function (bool $withTrait): void {
    $this->moonshineCore->getConfig()->contentSpacing(! $withTrait);
    $root = Div::make([FlexibleRender::make('Already spaced')])->class('custom space-elements extra');
    $page = makeSpacingTestPage($this->moonshineCore, withTrait: $withTrait)->setComponents([$root]);

    expect(layoutContentSpacingWrappers($page))->toBeEmpty()
        ->and($page->getComponents()->first())->toBe($root)
        ->and(layoutContentFragmentHtml($page))->toContain('Already spaced');
})->with(['config' => false, 'trait' => true]);

it('does not mistake similar class names for the spacing class', function (string $class): void {
    $this->moonshineCore->getConfig()->contentSpacing();
    $page = makeSpacingTestPage($this->moonshineCore)
        ->setComponents([Div::make([])->class($class)]);

    expect(layoutContentSpacingWrappers($page))->toHaveCount(1);
})->with(['no-space-elements', 'space-elements-custom']);

it('does not wrap getComponents when only the config flag is enabled', function (): void {
    $this->moonshineCore->getConfig()->contentSpacing();

    $page = makeSpacingTestPage($this->moonshineCore);
    $components = $page->getComponents();

    expect($components)->toHaveCount(2)
        ->and($components->first())->not->toBeInstanceOf(Div::class)
        ->and(layoutContentSpacingWrappers($page))->toHaveCount(1);
});
