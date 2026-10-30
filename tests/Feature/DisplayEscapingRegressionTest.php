<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Card;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Layout\Footer;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\InputExtensions\InputExt;
use MoonShine\UI\InputExtensions\InputPrefix;

it('renders link views with string and prepared slots', function (string $view, string $slotType): void {
    $markup = '<b>A & B</b>';
    $slot = match ($slotType) {
        'html' => new HtmlString(html: $markup),
        'component' => new ComponentSlot(contents: $markup),
        'htmlable' => new class () implements Htmlable {
            public function toHtml(): string
            {
                return '<b>A & B</b>';
            }
        },
        default => $markup,
    };

    $html = moonshine()->getRenderer()->render('moonshine::components.' . $view, [
        'attributes' => new MoonShineComponentAttributeBag(attributes: ['href' => '/help']),
        'slot' => $slot,
        'label' => 'Fallback',
    ])->render();

    expect($html)
        ->toContain($slotType === 'string' ? '&lt;b&gt;A &amp; B&lt;/b&gt;' : $markup)
        ->not->toContain('Fallback');
})->with(['link-native', 'link-button'])->with(['string', 'html', 'component', 'htmlable']);

it('renders link labels when the slot is missing or empty', function (string $view, string $slotType): void {
    $data = [
        'attributes' => new MoonShineComponentAttributeBag(attributes: ['href' => '/help']),
        'label' => '<b>Fallback</b>',
    ];

    if ($slotType !== 'missing') {
        $data['slot'] = match ($slotType) {
            'component' => new ComponentSlot(contents: '  '),
            'null' => null,
            default => '',
        };
    }

    expect(moonshine()->getRenderer()->render('moonshine::components.' . $view, $data)->render())
        ->toContain('&lt;b&gt;Fallback&lt;/b&gt;')
        ->not->toContain('<b>Fallback</b>');
})->with(['link-native', 'link-button'])->with(['missing', 'string', 'component', 'null']);

it('uses labels for empty anonymous link slots without changing explicit slots', function (string $view): void {
    expect(Blade::render('<x-moonshine::' . $view . ' label="Fallback" />'))->toContain('Fallback');
    expect(Blade::render('<x-moonshine::' . $view . ' label="Fallback">0</x-moonshine::' . $view . '>'))
        ->toContain('0')->not->toContain('Fallback');
})->with(['link-native', 'link-button']);

it('preserves custom extension attributes when rendering a field', function (): void {
    $field = new class (label: 'Name', column: 'name') extends Text {
        public function getExtensionsAttributes(): ComponentAttributesBagContract
        {
            $attributes = parent::getExtensionsAttributes();
            $attributes->set('data-custom-extension', 'present');
            $attributes->set('x-init', 'customExtensionInit()');

            return $attributes;
        }
    };

    expect((string) $field->prefix(ext: 'kg'))
        ->toContain('data-custom-extension="present"', 'customExtensionInit()', 'kg');
});

it('prepares collapse titles replaced through custom view data', function (bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $collapse = Collapse::make(label: 'Old title')
        ->escapeLabel($escape)
        ->customView(view: 'moonshine::components.collapse', data: ['title' => '<b>New & title</b>']);

    expect($collapse->toArray()['title'])->toBe('<b>New & title</b>');
    expect((string) $collapse)
        ->toContain($escape ? '&lt;b&gt;New &amp; title&lt;/b&gt;' : '<b>New & title</b>')
        ->not->toContain('Old title');
})->with([true, false]);

it('preserves an explicit prepared collapse label', function (): void {
    $collapse = Collapse::make(label: 'Old title')->customView(view: 'moonshine::components.collapse', data: [
        'title' => 'New title',
        'labelHtml' => new HtmlString(html: '<b>Prepared title</b>'),
    ]);

    expect((string) $collapse)->toContain('<b>Prepared title</b>')->not->toContain('Old title', 'New title');
});

it('prepares footer menu labels replaced through custom view data', function (bool $escape, string $menuType): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $menu = ['/help' => '<b>New & help</b>'];
    $customMenu = match ($menuType) {
        'collection' => collect($menu),
        'generator' => (static function () use ($menu): Generator {
            yield from $menu;
        })(),
        default => $menu,
    };
    $footer = Footer::make(menu: ['/help' => 'Old help'])
        ->escapeLabel($escape)
        ->customView(view: 'moonshine::components.layout.footer', data: ['menu' => $customMenu]);

    $footer->toArray();
    json_encode($footer, JSON_THROW_ON_ERROR);

    expect((string) $footer)
        ->toContain($escape ? '&lt;b&gt;New &amp; help&lt;/b&gt;' : '<b>New & help</b>')
        ->not->toContain('Old help');
})->with([true, false])->with(['array', 'collection', 'generator']);

it('preserves custom card iterator values across serialization and rendering', function (bool $escape): void {
    $values = (static function (): Generator {
        yield '<b>Name</b>' => 'Alice';
    })();
    $card = Card::make()
        ->customView(view: 'moonshine::components.card', data: [
            'values' => $values,
            'escapeLabel' => $escape,
        ]);

    expect($card->toArray()['values'])->toBe(['<b>Name</b>' => 'Alice']);
    json_encode($card, JSON_THROW_ON_ERROR);

    expect((string) $card)->toContain($escape ? '&lt;b&gt;Name&lt;/b&gt;:' : '<b>Name</b>:', 'Alice');
    expect((string) (clone $card))->toContain('Alice');

    $card->flushRenderCache()->customView(view: 'moonshine::components.card', data: ['values' => ['New' => 'Bob']]);

    expect((string) $card)->toContain('New:', 'Bob')->not->toContain('Alice');
})->with([true, false]);

it('preserves an explicit prepared footer menu label', function (): void {
    $footer = Footer::make(menu: ['/help' => 'Old help'])
        ->customView(view: 'moonshine::components.layout.footer', data: [
            'menu' => ['/help' => 'New help'],
            'menuLabelsHtml' => ['/help' => new HtmlString(html: '<b>Prepared help</b>')],
        ]);

    expect((string) $footer)->toContain('<b>Prepared help</b>')->not->toContain('Old help', 'New help');
});

it('returns stored input extensions so callers can configure them', function (string $extension): void {
    $field = Text::make('Title', 'title')->extension(new $extension('<b>Decoration</b>'));

    $field->getExtensions()->get($extension)->escape(false);

    expect($field->getExtensions()->get($extension)->isEscape())->toBeFalse();
    expect((string) $field)->toContain('<b>Decoration</b>');
})->with(['prefix' => InputPrefix::class, 'ext' => InputExt::class]);

it('applies the button label preference to titles falling back to the label', function (string $container, bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);

    $button = ActionButton::make('<b>Edit</b>', '/edit')->escapeLabel($escape);
    $button = $container === 'modal'
        ? $button->inModal(title: static fn (): ?string => null)
        : $button->inOffCanvas(title: static fn (): ?string => null);
    $component = $container === 'modal' ? $button->getModal() : $button->getOffCanvas();

    expect((string) $component)->toContain($escape ? '&lt;b&gt;Edit&lt;/b&gt;' : '<b>Edit</b>');
})->with(['modal', 'offcanvas'])->with([true, false]);

it('renders link views without a slot when no composer prepares it', function (string $view): void {
    $template = file_get_contents(__DIR__ . '/../../src/UI/resources/views/components/' . $view . '.blade.php');

    expect(Blade::render($template, ['attributes' => new MoonShineComponentAttributeBag(attributes: ['href' => '/help'])]))
        ->toContain('href="/help"');
})->with(['link-native', 'link-button']);

it('keeps prepared preview markup in escaped link names', function (Closure $make): void {
    $preview = (string) $make()->preview();
    $field = $make()->link('/state', name: static fn (mixed $value): string => '<i>State</i> ' . $value);

    expect($preview)->toContain('<div');
    expect((string) $field->preview())->toContain('&lt;i&gt;State&lt;/i&gt; ' . $preview);
})->with([
    'checkbox' => [static fn (): Checkbox => Checkbox::make('Active', 'active')->setValue(true)],
    'color' => [static fn (): Color => Color::make('Color', 'color')->setValue('#ff0000')],
]);

it('returns Htmlable render decorations through the Renderable contract', function (string $type): void {
    $field = Text::make('Title', 'title');

    match ($type) {
        'callback' => $field
            ->beforeRender(static fn (): HtmlString => new HtmlString('<b>Before</b>'))
            ->afterRender(static fn (): HtmlString => new HtmlString('<b>After</b>')),
        'xIf' => $field->xIf('type', '=', '1'),
    };

    $before = $field->getBeforeRender();
    $after = $field->getAfterRender();

    expect($before)->toBeInstanceOf(Renderable::class)->and($after)->toBeInstanceOf(Renderable::class);
    expect((string) $field)->toContain($before->render(), $after->render());

    if ($type === 'xIf') {
        expect($before->render())->toStartWith('<template x-if=')->and($after->render())->toBe('</template>');
    }
})->with(['callback', 'xIf']);

it('outputs prepared footer menu labels', function (string $source): void {
    $menu = ['/docs' => new HtmlString('<b>Docs</b>')];
    $footer = $source === 'constructor' ? Footer::make(menu: $menu) : Footer::make()->menu($menu);

    expect((string) $footer)->toContain('<b>Docs</b>');
})->with(['constructor', 'menu']);

it('accepts a tab label preference in the constructor', function (bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $tab = Tab::make('<b>A</b>', [], escapeLabel: $escape);

    expect($tab->isEscapeLabel())->toBe($escape)
        ->and($tab->getLabelHtml()->toHtml())->toBe($escape ? '&lt;b&gt;A&lt;/b&gt;' : '<b>A</b>');
})->with([true, false]);

it('resolves escaping defaults again after configuration changes', function (): void {
    $config = moonshine()->getConfig();

    expect($config->isEscapeLabel())->toBeTrue();

    $config->escapeLabel(false);
    expect($config->isEscapeLabel())->toBeFalse();

    $config['escapes.label'] = 'on';
    expect($config->isEscapeLabel())->toBeTrue();

    $escape = false;
    $config->set('escapes.label', static function () use (&$escape): bool {
        return $escape;
    });
    expect($config->isEscapeLabel())->toBeFalse();

    $escape = true;
    expect($config->isEscapeLabel())->toBeTrue();
});
