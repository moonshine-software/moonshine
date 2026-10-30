<?php

declare(strict_types=1);

use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentSlot;
use MoonShine\UI\Components\Link;

it('renders custom link labels using the local escaping preference', function (bool $button, bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $label = '<b>New & label</b>';
    $link = Link::make(href: '/help', label: 'Old label')
        ->escapeLabel($escape)
        ->when($button, static fn (Link $link): Link => $link->button())
        ->customView(view: 'moonshine::components.link-native', data: ['label' => $label]);

    $data = $link->toArray();

    expect($link->getLabel())->toBe('Old label')
        ->and($data['label'])->toBe($label)
        ->and($data['slot'])->toBeInstanceOf(ComponentSlot::class);
    expect((string) $link)
        ->toContain($escape ? '&lt;b&gt;New &amp; label&lt;/b&gt;' : $label)
        ->not->toContain('Old label');
})->with([false, true])->with([false, true]);

it('renders explicit prepared custom link labels', function (bool $button, bool $prepared): void {
    $markup = '<b>Prepared & label</b>';
    $link = Link::make(href: '/help', label: 'Old label')
        ->when($button, static fn (Link $link): Link => $link->button())
        ->customView(view: 'moonshine::components.link-native', data: [
            'label' => 'Raw replacement',
            'labelHtml' => $prepared ? new HtmlString(html: $markup) : $markup,
        ]);

    expect((string) $link)
        ->toContain($prepared ? $markup : '&lt;b&gt;Prepared &amp; label&lt;/b&gt;')
        ->not->toContain('Old label', 'Raw replacement');
})->with([false, true])->with([false, true]);

it('preserves explicit custom link slots over replacement labels', function (bool $button, bool $prepared): void {
    $markup = '<strong>Explicit & slot</strong>';
    $link = Link::make(href: '/help', label: 'Old label')
        ->when($button, static fn (Link $link): Link => $link->button())
        ->customView(view: 'moonshine::components.link-native', data: [
            'label' => 'Replacement label',
            'labelHtml' => new HtmlString(html: '<b>Prepared label</b>'),
            'slot' => $prepared ? new ComponentSlot(contents: $markup) : $markup,
        ]);

    expect((string) $link)
        ->toContain($prepared ? $markup : '&lt;strong&gt;Explicit &amp; slot&lt;/strong&gt;')
        ->not->toContain('Old label', 'Replacement label', 'Prepared label');
})->with([false, true])->with([false, true]);

it('keeps empty and zero replacement link labels', function (bool $button, string $label): void {
    $link = Link::make(href: '/help', label: 'Old label')
        ->when($button, static fn (Link $link): Link => $link->button())
        ->customView(view: 'moonshine::components.link-native', data: ['label' => $label]);

    $document = Dom\HTMLDocument::createFromString('<!DOCTYPE html><html><head></head><body>' . $link . '</body></html>');

    expect(trim($document->querySelector('a')->textContent))->toBe($label);
})->with([false, true])->with(['', '0']);

it('falls back to replacement labels only for empty custom slots', function (bool $button, ?string $slot): void {
    $link = Link::make(href: '/help', label: 'Old label')
        ->when($button, static fn (Link $link): Link => $link->button())
        ->customView(view: 'moonshine::components.link-native', data: [
            'label' => 'Replacement label',
            'slot' => $slot,
        ]);

    $document = Dom\HTMLDocument::createFromString('<!DOCTYPE html><html><head></head><body>' . $link . '</body></html>');

    expect(trim($document->querySelector('a')->textContent))->toBe($slot === '0' ? '0' : 'Replacement label');
})->with([false, true])->with([null, '', '  ', '0']);
