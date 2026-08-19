<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Url;

uses()->group('fields');
uses()->group('escape');

function applyToModel(Field $field, string $value): Model
{
    fakeRequest(parameters: ['field_name' => $value]);

    return $field->apply(
        TestResourceBuilder::new()->fieldApply($field),
        new class () extends Model {
            protected $fillable = [
                'field_name',
            ];
        }
    );
}

it('stores the typed value verbatim', function (string $class): void {
    $value = 'musique d\'ambiance & "quotes" <b>';

    expect(applyToModel($class::make('Field name'), $value))
        ->field_name
        ->toBe($value);
})->with([
    [Text::class],
    [Textarea::class],
    [Email::class],
    [Url::class],
    [Phone::class],
    [Slug::class],
]);

it('does not compound encoding across repeated saves', function (string $class): void {
    $value = 'a & b';

    $first = applyToModel($class::make('Field name'), $value)->field_name;
    $second = applyToModel($class::make('Field name'), $first)->field_name;

    expect($second)->toBe($first);
})->with([
    [Text::class],
    [Textarea::class],
]);

it('keeps a url query string intact', function (): void {
    expect(applyToModel(Url::make('Field name'), 'https://example.com/?a=1&b=2'))
        ->field_name
        ->toBe('https://example.com/?a=1&b=2');
});

it('escapes the text input value attribute on render', function (): void {
    $payload = '" onfocus="alert(1)" autofocus x="';

    expect((string) Text::make('Field name')->fill($payload)->render())
        ->toContain('&quot; onfocus=&quot;alert(1)&quot; autofocus x=&quot;')
        ->not->toContain('" onfocus="alert(1)"');
});

it('escapes the textarea value on render', function (): void {
    expect((string) Textarea::make('Field name')->fill('<script>alert(1)</script>')->render())
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->not->toContain('<script>');
});

it('escapes both the href and the label of a url preview', function (): void {
    expect((string) Url::make('Field name')->fill('https://example.com/?a=1&b=2"><script>alert(1)</script>')->previewMode()->render())
        ->toContain('href="https://example.com/?a=1&amp;b=2&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;"')
        ->not->toContain('<script>');
});

it('escapes the preview value', function (string $class): void {
    expect((string) $class::make('Field name')->fill('<script>alert(1)</script>')->previewMode()->render())
        ->not->toContain('<script>');
})->with([
    [Text::class],
    [Textarea::class],
]);

it('renders the raw value when unescaped', function (string $class): void {
    expect((string) $class::make('Field name')->unescape()->fill('<b>bold</b>')->previewMode()->render())
        ->toContain('<b>bold</b>');
})->with([
    [Text::class],
    [Textarea::class],
]);
