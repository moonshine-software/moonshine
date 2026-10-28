<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Traits\Fields\WithEscapedValue;

uses()->group('fields');

mutates(Text::class, Textarea::class, WithEscapedValue::class);

beforeEach(function () {
    $this->item = createItem();
});

it('empty value save', function () {
    $resource = app(TestItemResource::class);

    $data = [
        'content' => '',
    ];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->content)
        ->toBeNull()
    ;
});

it('saves text using the configured apply escaping', function (string $mode, string $expected): void {
    $field = Text::make('Name', 'name');

    match ($mode) {
        'unescape' => $field->unescape(),
        'raw apply' => $field->escapeOnApply(static fn (): bool => false),
        'escaped apply' => $field->unescape()->escapeOnApply(static fn (): bool => true),
        default => $field,
    };

    $resource = addFieldsToTestResource($field);

    asAdmin()->put($resource->getRoute('crud.update', $this->item->getKey()), [
        'name' => '<b>Tom & "Jerry"</b>',
    ])->assertRedirect();

    expect($this->item->refresh()->name)->toBe($expected);
})->with([
    'default' => ['default', '&lt;b&gt;Tom &amp; &quot;Jerry&quot;&lt;/b&gt;'],
    'preview preference keeps save escaping' => ['unescape', '&lt;b&gt;Tom &amp; &quot;Jerry&quot;&lt;/b&gt;'],
    'explicit raw save' => ['raw apply', '<b>Tom & "Jerry"</b>'],
    'explicit escaped save' => ['escaped apply', '&lt;b&gt;Tom &amp; &quot;Jerry&quot;&lt;/b&gt;'],
]);

it('previews text using the field escaping preference', function (string $mode, string $expected): void {
    $field = Text::make('Name', 'name');

    match ($mode) {
        'raw' => $field->unescape(),
        'escaped again' => $field->unescape()->escape(),
        default => $field,
    };

    $this->item->name = '<b>Tom & "Jerry"</b>';
    $field->fillData($this->item);

    expect((string) $field->preview())->toContain('>' . $expected . '</div>');
})->with([
    'default' => ['default', '&lt;b&gt;Tom &amp; &quot;Jerry&quot;&lt;/b&gt;'],
    'raw' => ['raw', '<b>Tom & "Jerry"</b>'],
    'escaped again' => ['escaped again', '&lt;b&gt;Tom &amp; &quot;Jerry&quot;&lt;/b&gt;'],
]);

it('escapes textarea values on save unless explicitly disabled', function (bool $raw, string $expected): void {
    $field = Textarea::make('Content', 'content');

    if ($raw) {
        $field->escapeOnApply(static fn (): bool => false);
    }

    $resource = addFieldsToTestResource($field);

    asAdmin()->put($resource->getRoute('crud.update', $this->item->getKey()), [
        'content' => '<b>Tom & "Jerry"</b>',
    ])->assertRedirect();

    expect($this->item->refresh()->content)->toBe($expected);
})->with([
    'escaped' => [false, '&lt;b&gt;Tom &amp; &quot;Jerry&quot;&lt;/b&gt;'],
    'raw' => [true, '<b>Tom & "Jerry"</b>'],
]);

it('renders text input extensions with the field value', function (): void {
    $html = (string) Text::make('Amount', 'amount')->prefix('USD')->suffix('net')->setValue('12')->render();

    expect($html)->toContain('USD')->toContain('net')->toContain('value="12"');
});
