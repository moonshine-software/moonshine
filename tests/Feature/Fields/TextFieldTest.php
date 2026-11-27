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

it('shows saved text unchanged in the form and keeps it when resaving', function (string $value, string $escaped): void {
    $resource = app(TestItemResource::class);
    $url = $resource->getRoute('crud.update', $this->item->getKey());
    $formValues = static function () use ($resource): array {
        $response = asAdmin()->get($resource->getFormPageUrl(test()->item->getKey()))->assertOk();
        $document = Dom\HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);

        return [
            'name' => $document->querySelector('main input[name="name"]')->getAttribute('value'),
            'content' => $document->querySelector('main textarea[name="content"]')->textContent,
        ];
    };

    asAdmin()->put($url, ['name' => $value, 'content' => $value])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect($this->item->refresh()->name)->toBe($escaped)
        ->and($this->item->content)->toBe($escaped)
        ->and($formValues())->toBe(['name' => $value, 'content' => $value]);

    asAdmin()->put($url, [...$formValues(), 'public_at' => '2026-01-02'])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect((string) $this->item->refresh()->public_at)->toStartWith('2026-01-02')
        ->and($this->item->name)->toBe($escaped)
        ->and($this->item->content)->toBe($escaped)
        ->and($formValues())->toBe(['name' => $value, 'content' => $value]);
})->with([
    'markup' => ['<b>Tom & "Jerry\'s"</b>', '&lt;b&gt;Tom &amp; &quot;Jerry&#039;s&quot;&lt;/b&gt;'],
    'typed entities' => ['Use &amp; for &, &#60;b&#62; &nbsp;', 'Use &amp;amp; for &amp;, &amp;#60;b&amp;#62; &amp;nbsp;'],
]);

it('displays saved text once in index and detail previews', function (): void {
    $this->item->forceFill([
        'name' => '&lt;b&gt;Tom &amp; Jerry&lt;/b&gt; &amp;amp;',
    ])->save();

    $resource = app(TestItemResource::class);

    foreach ([$resource->getIndexPageUrl(), $resource->getDetailPageUrl($this->item->getKey())] as $page) {
        $document = Dom\HTMLDocument::createFromString(
            asAdmin()->get($page)->assertOk()->getContent(),
            LIBXML_NOERROR,
        );

        expect($document->querySelector('main')->textContent)
            ->toContain('<b>Tom & Jerry</b> &amp;')
            ->not->toContain('&lt;b&gt;');
    }
});

it('keeps submitted old values unchanged in the form', function (): void {
    $resource = app(TestItemResource::class);
    $old = 'Use &amp; for &';

    $response = asAdmin()
        ->withSession(['_old_input' => ['name' => $old, 'content' => $old]])
        ->get($resource->getFormPageUrl($this->item->getKey()))
        ->assertOk();

    $document = Dom\HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);

    expect($document->querySelector('main input[name="name"]')->getAttribute('value'))->toBe($old)
        ->and($document->querySelector('main textarea[name="content"]')->textContent)->toBe($old);
});

it('keeps saved entities when escaping on apply is disabled', function (): void {
    moonshine()->getConfig()->escapeValueOnApply(false);

    $this->item->forceFill(['name' => 'A &amp; B', 'content' => 'A &amp; B'])->save();

    $resource = app(TestItemResource::class);
    $response = asAdmin()->get($resource->getFormPageUrl($this->item->getKey()))->assertOk();
    $document = Dom\HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);

    expect($document->querySelector('main input[name="name"]')->getAttribute('value'))->toBe('A &amp; B')
        ->and($document->querySelector('main textarea[name="content"]')->textContent)->toBe('A &amp; B');
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

it('refreshes after apply with the filled clone, a custom callback or not at all', function (): void {
    $field = Text::make('Name', 'name')->refreshAfterApply();
    $filled = (clone $field)->setValue('Saved');

    expect($field->isOnRefreshAfterApply())->toBeTrue()
        ->and($filled->resolveRefreshAfterApply())->toBe($filled)
        ->and($field->disableRefreshAfterApply()->isOnRefreshAfterApply())->toBeFalse()
        ->and($field->resolveRefreshAfterApply())->toBe($field);

    $custom = Text::make('Name', 'name')
        ->refreshAfterApply(static fn (Text $field): Text => (clone $field)->setValue('Custom'));

    expect((clone $custom)->resolveRefreshAfterApply()->toValue())->toBe('Custom');
});
