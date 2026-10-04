<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Card;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Components\OffCanvas;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;

it('preserves global and local display preferences in direct Blade components', function (string $template, string $setting, ?bool $global, ?bool $local, bool $escaped): void {
    if ($global !== null) {
        moonshine()->getConfig()->{$setting}($global);
    }

    $markup = '<b data-probe="display">A &amp; B</b>';
    $html = Blade::render($template, ['markup' => $markup, 'local' => $local]);

    expect($html)->toContain($escaped ? '&lt;b data-probe=&quot;display&quot;&gt;A &amp;amp; B&lt;/b&gt;' : $markup);
    if ($escaped) {
        expect($html)->not->toContain($markup);
    }
})->with([
    'fieldset' => ['<x-moonshine::form.fieldset :label="$markup" :escape-label="$local" />', 'escapeLabel'],
    'hint text' => ['<x-moonshine::display-text :value="$markup" :escape="$local" :display-type="\MoonShine\UI\Enums\DisplayTextType::HINT" />', 'escapeHint'],
    'prefix' => ['<x-moonshine::form.input-extensions.prefix :value="$markup" :escape-prefix="$local" />', 'escapePrefix'],
    'suffix' => ['<x-moonshine::form.input-extensions.ext :value="$markup" :escape-suffix="$local" />', 'escapeSuffix'],
    'table' => ['<x-moonshine::table :columns="[\'name\' => $markup]" :values="[[\'name\' => \'Value\']]" :escape-label="$local" />', 'escapeLabel'],
    'menu divider' => ['<x-moonshine::menu.divider :label="$markup" :escape-label="$local" />', 'escapeLabel'],
    'metric' => ['<x-moonshine::metrics.value :title="$markup" :escape-label="$local" />', 'escapeLabel'],
])->with([
    'default' => [null, null, true],
    'global enabled' => [true, null, true],
    'global disabled' => [false, null, false],
    'local enabled' => [false, true, true],
    'local disabled' => [true, false, false],
]);

it('honors local label preferences in class-backed Blade components', function (string $template, bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $label = '<b>A &amp; B</b>';
    $html = Blade::render($template, ['label' => $label, 'escape' => $escape]);

    expect($html)->toContain($escape ? '&lt;b&gt;A &amp;amp; B&lt;/b&gt;' : $label);
    if ($escape) {
        expect($html)->not->toContain($label);
    }
})->with([
    'action button' => '<x-moonshine::action-button :label="$label" :escape-label="$escape" />',
    'heading' => '<x-moonshine::heading :label="$label" :escape-label="$escape" />',
    'divider' => '<x-moonshine::layout.divider :label="$label" :escape-label="$escape" />',
    'box' => '<x-moonshine::layout.box :title="$label" :escape-label="$escape" />',
    'collapse' => '<x-moonshine::collapse :label="$label" :escape-label="$escape" />',
    'link' => '<x-moonshine::link href="#" :label="$label" :escape-label="$escape" />',
    'metric' => '<x-moonshine::metrics.wrapped.value-metric :label="$label" :escape-label="$escape" />',
])->with([true, false]);

it('prepares labels for card values replaced through custom view data', function (string $preference, bool $escape): void {
    moonshine()->getConfig()->escapeLabel($preference === 'global' ? $escape : ! $escape);
    $label = '<b>A &amp; B</b>';
    $values = [$label => '<em>Alice</em>'];
    $overrides = ['values' => $values];
    if ($preference === 'card') {
        $overrides['escapeLabel'] = $escape;
    } elseif ($preference === 'row') {
        $overrides['escapeValueLabels'] = [$label => $escape];
    }

    $card = Card::make(values: ['Old' => 'Discarded'])->customView('moonshine::components.card', $overrides);

    expect($card->toArray()['values'])->toBe($values);
    expect((string) $card)
        ->toContain($escape ? '&lt;b&gt;A &amp;amp; B&lt;/b&gt;' : $label, '<em>Alice</em>')
        ->not->toContain('Discarded');
})->with(['global', 'card', 'row'])->with([true, false]);

it('preserves trusted Blade slots in hints and empty headings', function (): void {
    expect(Blade::render('<x-moonshine::form.hint><strong>Help</strong></x-moonshine::form.hint>'))
        ->toContain('<strong>Help</strong>');
    expect(Blade::render('<x-moonshine::heading><strong>Heading</strong></x-moonshine::heading>'))
        ->toContain('<strong>Heading</strong>');
    expect((string) Heading::make(''))->not->toContain('&lt;');
});

it('preserves an explicit link slot over its label', function (string $component): void {
    $html = Blade::render('<x-moonshine::' . $component . ' href="#" label="Fallback"><strong>Slot</strong></x-moonshine::' . $component . '>');

    expect($html)->toContain('<strong>Slot</strong>')->not->toContain('Fallback');
})->with(['link', 'link-native', 'link-button']);

it('prepares card labels without modifying value keys or applying escaping twice', function (bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $label = '<b>A &amp; B</b>';
    $values = [$label => '<em>Value</em>'];
    $card = Card::make(values: $values)->escapeValueLabels([$label => $escape]);

    expect($card->toArray()['values'])->toBe($values);
    expect((string) $card)->toContain($escape ? '&lt;b&gt;A &amp;amp; B&lt;/b&gt;' : $label, '<em>Value</em>');
    expect(Blade::render('<x-moonshine::card :values="$values" :escape-value-labels="$preferences" />', [
        'values' => $values,
        'preferences' => [$label => $escape],
    ]))->toContain($escape ? '&lt;b&gt;A &amp;amp; B&lt;/b&gt;' : $label, '<em>Value</em>');
})->with([true, false]);

it('preserves raw tab labels in serialized state and prepares their display separately', function (bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $label = '<b>A &amp; B</b>';
    $tab = Tab::make($label)->escapeLabel($escape);
    $data = json_decode(json_encode($tab, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

    expect($data['label'])->toBe($label);
    expect((string) Tabs::make([$tab]))->toContain($escape ? '&lt;b&gt;A &amp;amp; B&lt;/b&gt;' : $label);
})->with([true, false]);

it('keeps HTML button types separate from display text types', function (string $type, bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $label = '<b>A & B</b>';
    $expected = $escape ? '&lt;b&gt;A &amp; B&lt;/b&gt;' : $label;

    expect((string) ActionButton::make($label)->customAttributes(['type' => $type])->escapeLabel($escape))
        ->toContain('type="' . $type . '"', $expected);
    expect(Blade::render('<x-moonshine::action-button :label="$label" :type="$type" :escape-label="$escape" />', [
        'label' => $label,
        'type' => $type,
        'escape' => $escape,
    ]))->toContain('type="' . $type . '"', $expected);
})->with(['submit', 'button', 'reset'])->with([true, false]);

it('escapes Blade slots only when escaping is requested explicitly', function (string $template, ?bool $escape): void {
    $html = Blade::render($template, ['escape' => $escape]);

    if ($escape === true) {
        expect($html)->toContain('&lt;strong&gt;Slot&lt;/strong&gt;')->not->toContain('<strong>Slot</strong>');
    } else {
        expect($html)->toContain('<strong>Slot</strong>');
    }
})->with([
    'hint' => '<x-moonshine::form.hint :escape-hint="$escape"><strong>Slot</strong></x-moonshine::form.hint>',
    'link button' => '<x-moonshine::link-button href="#" :escape-label="$escape"><strong>Slot</strong></x-moonshine::link-button>',
    'native link' => '<x-moonshine::link-native href="#" :escape-label="$escape"><strong>Slot</strong></x-moonshine::link-native>',
])->with([true, false, null]);

it('serializes prepared labels as strings next to raw labels', function (): void {
    $data = json_decode(json_encode(Heading::make('<b>A & B</b>'), JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

    expect($data['label'])->toBe('<b>A & B</b>')
        ->and($data['labelHtml'])->toBe('&lt;b&gt;A &amp; B&lt;/b&gt;');
});

it('renders modal and off-canvas titles according to title preferences', function (string $component, ?bool $global, ?bool $local, bool $escaped): void {
    if ($global !== null) {
        moonshine()->getConfig()->escapeLabel($global);
    }

    $title = '<b>Title</b>';
    $element = $component::make($title);

    if ($local !== null) {
        $element->escapeTitle($local);
    }

    expect((string) $element)->toContain($escaped ? '&lt;b&gt;Title&lt;/b&gt;' : $title);
})->with([Modal::class, OffCanvas::class])->with([
    'default' => [null, null, true],
    'global disabled' => [false, null, false],
    'local enabled' => [false, true, true],
    'local disabled' => [true, false, false],
]);

it('derives modal and off-canvas titles from the button label preference', function (string $method, bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $button = ActionButton::make('<b>Open</b>')->escapeLabel($escape)->{$method}();
    $component = $method === 'inModal' ? $button->getModal() : $button->getOffCanvas();

    expect((string) $component)->toContain($escape ? '&lt;b&gt;Open&lt;/b&gt;' : '<b>Open</b>');
})->with(['inModal', 'inOffCanvas'])->with([true, false]);

it('keeps explicit modal titles on the title preference', function (): void {
    $button = ActionButton::make('<b>Open</b>')->unescapeLabel()->inModal(title: static fn (): string => '<b>Record</b>');

    expect((string) $button->getModal())->toContain('&lt;b&gt;Record&lt;/b&gt;')->not->toContain('<b>Record</b>');
});
