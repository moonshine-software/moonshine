<?php

declare(strict_types=1);

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

it('sanitizes dangerous URLs in field previews', function (): void {
    $field = Url::make('Url');
    $item = new class () extends Model {
        public string $url = 'javascript:alert(1)';
    };

    fillFromModel($field, $item);

    expect((string) $field->preview())
        ->toContain('href="#"')
        ->not->toContain('javascript:alert(1)');
});
