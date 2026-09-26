<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\MenuManager\MenuDivider;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

uses()->group('fields');

dataset('display parts', ['Label', 'Hint', 'Prefix', 'Suffix', 'BeforeRender', 'AfterRender']);

dataset('display escape settings', [
    'default' => [null, null, true],
    'global enabled' => [true, null, true],
    'global disabled' => [false, null, false],
    'local disabled' => [true, false, false],
    'local enabled' => [false, true, true],
]);

it('renders field text according to global and local escaping settings', function (string $part, ?bool $global, ?bool $local, bool $escaped) {
    $config = moonshine()->getConfig();
    $method = "escape$part";

    if ($global !== null) {
        $config->{$method}($global);
    }

    $markup = '<b data-probe="display">A & B</b>';
    $field = Text::make('Title', 'title');

    match ($part) {
        'Label' => $field->setLabel($markup),
        'Hint' => $field->hint($markup),
        'Prefix' => $field->prefix($markup),
        'Suffix' => $field->suffix($markup),
        'BeforeRender' => $field->beforeRender(fn () => $markup),
        'AfterRender' => $field->afterRender(fn () => $markup),
    };

    if ($local !== null) {
        $local ? $field->{$method}() : $field->{"unescape$part"}();
    }

    $html = (string) $field;

    expect($html)->toContain($escaped ? '&lt;b data-probe=&quot;display&quot;&gt;A &amp; B&lt;/b&gt;' : $markup);
    expect($html)->not->toContain('&amp;lt;b');

    if ($escaped) {
        expect($html)->not->toContain($markup);
    }
})->with('display parts')->with('display escape settings');

it('can restore local escaping after disabling it', function (string $part) {
    $markup = '<em>Restore</em>';
    $field = Text::make('Title', 'title');

    match ($part) {
        'Label' => $field->setLabel($markup),
        'Hint' => $field->hint($markup),
        'Prefix' => $field->prefix($markup),
        'Suffix' => $field->suffix($markup),
        'BeforeRender' => $field->beforeRender(fn () => $markup),
        'AfterRender' => $field->afterRender(fn () => $markup),
    };

    $field->{"unescape$part"}()->{"escape$part"}();

    expect((string) $field)->toContain('&lt;em&gt;Restore&lt;/em&gt;')->not->toContain($markup);
})->with('display parts');

it('loads every escaping flag from the config repository', function (string $part) {
    $key = Illuminate\Support\Str::snake($part);
    $config = new MoonShineConfigurator(new Repository(['moonshine' => ["escape_$key" => false]]));

    expect($config->{"isEscape$part"}())->toBeFalse();
    $config->{"escape$part"}();
    expect($config->{"isEscape$part"}())->toBeTrue();
})->with('display parts');

it('keeps raw labels unchanged and renders translated closure labels safely', function () {
    app('translator')->addLines(['display.title' => '<b>Translated & label</b>'], 'en');
    $field = Text::make(fn () => 'title', 'field')->translatable('display');

    expect($field->getLabel())->toBe('<b>Translated & label</b>');
    expect($field->getColumn())->toBe('field');
    expect((string) $field)->toContain('&lt;b&gt;Translated &amp; label&lt;/b&gt;');
    expect($field->getLabel())->toBe('<b>Translated & label</b>');
});

it('renders empty labels without an empty label element', function () {
    expect((string) Text::make('', 'title'))->not->toContain('<label');
});

it('keeps field controls and renderable decorations as HTML', function () {
    $field = Checkbox::make('<b>Checkbox</b>', 'enabled')
        ->insideLabel()
        ->beforeRender(fn () => Link::make('/before', 'Before')->render())
        ->afterRender(fn () => Link::make('/after', 'After')->render());

    expect((string) $field)
        ->toContain('<input', 'type="checkbox"', '<a', 'href="/before"', 'href="/after"', '&lt;b&gt;Checkbox&lt;/b&gt;')
        ->not->toContain('&lt;input', '&lt;a');
});

it('does not let value unescape disable metadata escaping', function () {
    $field = Text::make('<b>Label</b>', 'title')->hint('<b>Hint</b>')->unescape();

    expect((string) $field)->toContain('&lt;b&gt;Label&lt;/b&gt;', '&lt;b&gt;Hint&lt;/b&gt;');
});

it('honors label overrides in table headers', function (bool $sortable, bool $vertical, bool $escape) {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $field = Text::make('<b>Header & label</b>', 'title')->escapeLabel($escape);

    if ($sortable) {
        $field->sortable();
    }

    $table = TableBuilder::make([$field], [['title' => 'Value']]);

    if ($vertical) {
        $table->vertical();
    }

    expect((string) $table)
        ->toContain($escape ? '&lt;b&gt;Header &amp; label&lt;/b&gt;' : '<b>Header & label</b>')
        ->not->toContain('&amp;lt;b');
})->with([true, false])->with([true, false])->with([true, false]);

it('escapes component labels and supports local opt out', function (string $kind, bool $escape) {
    $label = '<b>Component & label</b>';
    $component = match ($kind) {
        'button' => ActionButton::make($label, '/test'),
        'link' => Link::make('/test', $label),
        'collapse' => Collapse::make($label),
        'box' => Box::make($label),
        'heading' => Heading::make($label),
        'divider' => MenuDivider::make($label),
        'fieldset' => Fieldset::make($label),
        'textarea' => Textarea::make($label, 'content'),
        'tab' => Tab::make($label),
    };
    $component->escapeLabel($escape);

    if ($kind === 'tab') {
        $component = Tabs::make([$component]);
    }

    expect((string) $component)
        ->toContain($escape ? '&lt;b&gt;Component &amp; label&lt;/b&gt;' : $label)
        ->not->toContain('&amp;lt;b');
})->with(['button', 'link', 'collapse', 'box', 'heading', 'divider', 'fieldset', 'textarea', 'tab'])->with([true, false]);

it('preserves field label preferences in column selection', function (bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $field = Text::make('<b>Column label</b>', 'name')->escapeLabel($escape);
    $table = TableBuilder::make([$field], [['name' => 'Value']])->columnSelection();

    $document = new DOMDocument();
    @$document->loadHTML((string) $table);
    $xpath = new DOMXPath($document);
    $labels = $xpath->query('//div[contains(@class, "dropdown-body--column-selection")]//label[contains(@class, "form-label")]');

    expect($labels->length)->toBe(1);
    expect($xpath->query('.//b', $labels->item(0))->length)->toBe($escape ? 0 : 1);
    expect(trim($labels->item(0)->textContent))->toBe($escape ? '<b>Column label</b>' : 'Column label');
    expect($xpath->query('//th/b')->length)->toBe($escape ? 0 : 2);
})->with([true, false]);

it('keeps serialized column labels raw and excludes unavailable selections', function (): void {
    $table = TableBuilder::make([
        Text::make('<b>Name</b>', 'name')->unescapeLabel(),
        Text::make('Hidden', 'hidden')->canSee(static fn (): bool => false),
        Text::make('Disabled', 'disabled')->columnSelection(false),
        Text::make('', 'empty'),
    ], [['name' => 'Value']])->columnSelection();

    expect($table->toArray()['columns'])->toBe(['name' => '<b>Name</b>']);

    $document = new DOMDocument();
    @$document->loadHTML((string) $table);
    $xpath = new DOMXPath($document);
    $inputs = $xpath->query('//input[@data-column-selection-checker]');

    expect($inputs->length)->toBe(1);
    expect($inputs->item(0)->getAttribute('data-column'))->toBe('name');
});

it('escapes plain labels passed directly to Blade components', function () {
    $html = $this->blade('<x-moonshine::form.wrapper :label="$label" />', ['label' => '<b>Direct</b>']);

    $html->assertSee('&lt;b&gt;Direct&lt;/b&gt;', false)->assertDontSee('<b>Direct</b>', false);
});

it('preserves label overrides in cards', function (bool $escape) {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $field = Text::make('<b>Card & label</b>', 'title')->escapeLabel($escape);
    $cards = MoonShine\UI\Components\CardsBuilder::make([['title' => 'Value']], [$field]);

    expect((string) $cards)
        ->toContain($escape ? '&lt;b&gt;Card &amp; label&lt;/b&gt;' : '<b>Card & label</b>')
        ->not->toContain('&amp;lt;b');
})->with([true, false]);

it('keeps generated xIf templates executable', function () {
    $field = Text::make('Title', 'title')->xIf('enabled', '1');

    expect((string) $field)
        ->toContain('<template x-if=', '</template>', '<input')
        ->not->toContain('&lt;template', '&lt;/template');
});

it('keeps serialized labels as raw strings', function () {
    $field = Text::make('<b>Label</b>', 'title');
    $data = json_decode(json_encode($field, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

    expect($data['label'])->toBe('<b>Label</b>');
});

it('always escapes textarea aria labels even when display labels allow HTML', function () {
    $field = Textarea::make('" onfocus="alert(1)', 'content')->unescapeLabel();
    $document = new DOMDocument();
    @$document->loadHTML((string) $field);
    $textarea = $document->getElementsByTagName('textarea')->item(0);

    expect($textarea->getAttribute('aria-label'))->toBe('" onfocus="alert(1)');
    expect($textarea->hasAttribute('onfocus'))->toBeFalse();
});

it('resolves global preferences at render time and keeps categories independent', function () {
    $field = Text::make('<b>Label</b>', 'title')->hint('<b>Hint</b>');
    moonshine()->getConfig()->escapeLabel(false);

    expect((string) $field)->toContain('<b>Label</b>', '&lt;b&gt;Hint&lt;/b&gt;');
});

it('does not double escape link-decorated field previews', function (bool $unescape) {
    $field = Text::make('Title', 'title')->setValue('<b>A & B</b>')->link('/test')->withoutTextWrap();

    if ($unescape) {
        $field->unescape();
    }

    expect((string) $field->preview())
        ->toContain($unescape ? '<b>A & B</b>' : '&lt;b&gt;A &amp; B&lt;/b&gt;')
        ->not->toContain('&amp;lt;b');
})->with([true, false]);

it('does not double escape URL field previews', function () {
    $field = MoonShine\UI\Fields\Url::make('URL')->setValue('https://example.test/?a=1&b=2');

    expect((string) $field->preview())->toContain('a=1&amp;b=2')->not->toContain('&amp;amp;');
});

it('preserves text returned by preview link name callbacks', function (string $value): void {
    $field = Text::make('Title', 'title')->setValue($value)
        ->link('/test', name: static fn (string $preview): string => 'Open ' . $preview);

    $document = new DOMDocument();
    @$document->loadHTML((string) $field->preview());

    expect(trim($document->getElementsByTagName('a')->item(0)->textContent))->toBe('Open ' . $value);
    expect($document->getElementsByTagName('b')->length)->toBe(0);
})->with(['A & B', '<b>A & B</b>', 'A &amp; B', '"A" & \'B\'']);

it('escapes markup introduced by preview link names according to label preferences', function (bool $callback, bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $field = Text::make('Title', 'title')->setValue('A & B')->escapeLabel($escape)
        ->link('/test', name: $callback
            ? static fn (string $preview): string => '<strong>Open ' . $preview . '</strong>'
            : '<strong>Open A & B</strong>');

    $document = new DOMDocument();
    @$document->loadHTML((string) $field->preview());

    expect($document->getElementsByTagName('strong')->length)->toBe($escape ? 0 : 1);
    expect(trim($document->getElementsByTagName('a')->item(0)->textContent))
        ->toBe($escape ? '<strong>Open A & B</strong>' : 'Open A & B');
})->with([true, false])->with([true, false]);

it('preserves literal entities in static preview link names', function (): void {
    $field = Text::make('Title', 'title')->setValue('Value')->link('/test', name: 'A &amp; B');
    $document = new DOMDocument();
    @$document->loadHTML((string) $field->preview());

    expect(trim($document->getElementsByTagName('a')->item(0)->textContent))->toBe('A &amp; B');
});

it('escapes footer menu labels by default', function () {
    $footer = MoonShine\UI\Components\Layout\Footer::make()->menu(['/help' => '<b>Help</b>']);

    expect((string) $footer)->toContain('&lt;b&gt;Help&lt;/b&gt;')->not->toContain('<b>Help</b>');
});

it('escapes standalone Blade table column labels by default', function () {
    $this->blade('<x-moonshine::table :columns="$columns" :values="$values" />', [
        'columns' => ['name' => '<b>Name</b>'],
        'values' => [['name' => 'Example']],
    ])->assertSee('&lt;b&gt;Name&lt;/b&gt;', false)->assertDontSee('<b>Name</b>', false);
});
