<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Dom\HTMLElement;
use Illuminate\Database\Eloquent\Model;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Fields\Url;

it('replaces dangerous link schemes with a safe fallback', function (string $url): void {
    $html = (string) Link::make($url, 'Open')->render();

    expect($html)
        ->toContain('href="#"')
        ->not->toContain($url);
})->with([
    'javascript:alert(1)',
    'JaVaScRiPt:alert(1)',
    "java\nscript:alert(1)",
    "java\0script:alert(1)",
    'data:text/html,<script>alert(1)</script>',
    'vbscript:msgbox(1)',
]);

it('preserves safe link schemes', function (string $url): void {
    $html = (string) Link::make($url, 'Open')->render();

    expect($html)->toContain('href="' . $url . '"');
})->with([
    'https://cutcode.dev/docs',
    'http://cutcode.dev/docs',
    '//cdn.cutcode.dev/docs',
    '/admin/resources',
    '#details',
    'mailto:security@cutcode.dev',
    'tel:+1234567890',
]);

it('sanitizes field preview URLs while preserving their displayed value', function (): void {
    $field = Url::make('Url');
    $item = new class () extends Model {
        public string $url = 'javascript:alert(1)';
    };

    fillFromModel($field, $item);

    $document = HTMLDocument::createFromString(
        '<!DOCTYPE html><html><head></head><body>' . $field->preview() . '</body></html>'
    );
    $link = $document->getElementsByTagName('a')->item(0);

    expect($link)->toBeInstanceOf(HTMLElement::class)
        ->and($link->getAttribute('href'))->toBe('#')
        ->and($link->textContent)->toContain('javascript:alert(1)');
});
