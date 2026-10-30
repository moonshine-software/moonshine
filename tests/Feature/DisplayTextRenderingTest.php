<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Card;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Components\OffCanvas;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;

it('escapes plain strings and outputs prepared HTML in direct anonymous Blade components', function (string $template, ?bool $global, bool $prepared): void {
    if ($global !== null) {
        moonshine()->getConfig()->escapeLabel($global);
        moonshine()->getConfig()->escapeHint($global);
        moonshine()->getConfig()->escapePrefix($global);
        moonshine()->getConfig()->escapeSuffix($global);
    }

    $markup = '<b data-probe="display">A &amp; B</b>';
    $html = Blade::render($template, ['markup' => $prepared ? new HtmlString($markup) : $markup]);

    if ($prepared) {
        expect($html)->toContain($markup);
    } else {
        expect($html)->toContain('&lt;b data-probe=&quot;display&quot;&gt;A &amp;amp; B&lt;/b&gt;')->not->toContain($markup);
    }
})->with([
    'fieldset' => '<x-moonshine::form.fieldset :label="$markup" />',
    'wrapper' => '<x-moonshine::form.wrapper :label="$markup" />',
    'hint' => '<x-moonshine::form.hint :hint-html="$markup" />',
    'prefix' => '<x-moonshine::form.input-extensions.prefix :value="$markup" />',
    'suffix' => '<x-moonshine::form.input-extensions.ext :value="$markup" />',
    'table' => '<x-moonshine::table :columns="[\'name\' => $markup]" :values="[[\'name\' => \'Value\']]" />',
    'menu divider' => '<x-moonshine::menu.divider :label="$markup" />',
    'metric' => '<x-moonshine::metrics.value :title="$markup" />',
])->with([
    'default' => [null],
    'global enabled' => [true],
    'global disabled' => [false],
])->with([
    'plain string' => false,
    'prepared HTML' => true,
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

it('outputs Blade slots as trusted template output', function (string $template): void {
    expect(Blade::render($template))->toContain('<strong>Slot</strong>');
})->with([
    'hint' => '<x-moonshine::form.hint><strong>Slot</strong></x-moonshine::form.hint>',
    'link button' => '<x-moonshine::link-button href="#"><strong>Slot</strong></x-moonshine::link-button>',
    'native link' => '<x-moonshine::link-native href="#"><strong>Slot</strong></x-moonshine::link-native>',
]);

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
})->with(['modal' => Modal::class, 'off-canvas' => OffCanvas::class])->with([
    'default' => [null, null, true],
    'global disabled' => [false, null, true],
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
    moonshine()->getConfig()->escapeLabel(false);

    $button = ActionButton::make('<b>Open</b>')->unescapeLabel()->inModal(title: static fn (): string => '<b>Record</b>');

    expect((string) $button->getModal())->toContain('&lt;b&gt;Record&lt;/b&gt;')->not->toContain('<b>Record</b>');
});

it('keeps record-derived headings escaped when global label escaping is disabled', function (): void {
    moonshine()->getConfig()->escapeLabel(false);
    $label = '<img src=x onerror=alert(1)> & Name';

    expect((string) Heading::make($label))
        ->toContain('&lt;img src=x onerror=alert(1)&gt; &amp; Name')
        ->not->toContain($label);
    expect(Blade::render('<x-moonshine::heading :label="$label" />', ['label' => $label]))
        ->toContain('&lt;img src=x onerror=alert(1)&gt; &amp; Name')
        ->not->toContain($label);
    expect((string) Heading::make('<b>Trusted</b>')->unescapeLabel())->toContain('<b>Trusted</b>');
});

it('keeps confirmation record text escaped when global label escaping is disabled', function (): void {
    moonshine()->getConfig()->escapeLabel(false);
    $markup = '<img src=x onerror=alert(1)>';
    $button = ActionButton::make('Delete')->withConfirm(
        title: static fn (): string => 'Delete ' . $markup . '?',
        content: static fn (): string => 'Really delete ' . $markup . '?',
    );

    expect((string) $button->getModal())
        ->toContain('Delete &lt;img src=x onerror=alert(1)&gt;?', 'Really delete &lt;img src=x onerror=alert(1)&gt;?')
        ->not->toContain($markup);
});
