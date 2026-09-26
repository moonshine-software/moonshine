<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use MoonShine\UI\Components\Card;
use MoonShine\UI\Components\Heading;
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
