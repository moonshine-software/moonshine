<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Layout\Footer;
use MoonShine\UI\Fields\Text;

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

    expect((string) $footer)
        ->toContain($escape ? '&lt;b&gt;New &amp; help&lt;/b&gt;' : '<b>New & help</b>')
        ->not->toContain('Old help');
})->with([true, false])->with(['array', 'collection', 'generator']);

it('preserves an explicit prepared footer menu label', function (): void {
    $footer = Footer::make(menu: ['/help' => 'Old help'])
        ->customView(view: 'moonshine::components.layout.footer', data: [
            'menu' => ['/help' => 'New help'],
            'menuLabelsHtml' => ['/help' => new HtmlString(html: '<b>Prepared help</b>')],
        ]);

    expect((string) $footer)->toContain('<b>Prepared help</b>')->not->toContain('Old help', 'New help');
});
