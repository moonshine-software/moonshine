<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

uses()->group('fields');

it('loads escaping defaults and overrides from the configuration repository', function (): void {
    $defaults = require __DIR__ . '/../../../src/Laravel/config/moonshine.php';

    expect($defaults['escapes']['value'])->toBeTrue()
        ->and($defaults['escapes']['value_on_apply'])->toBeTrue();

    foreach ([[], $defaults, ['escapes' => ['value' => false, 'value_on_apply' => false]]] as $values) {
        $config = new MoonShineConfigurator(new Repository(['moonshine' => $values]));

        expect($config->isEscapeValue())->toBe($values['escapes']['value'] ?? true)
            ->and($config->isEscapeValueOnApply())->toBe($values['escapes']['value_on_apply'] ?? true);
    }
});

it('reads environment strings like the display escaping settings', function (mixed $value, bool $expected): void {
    $config = new MoonShineConfigurator(new Repository(['moonshine' => [
        'escapes' => ['value' => $value, 'value_on_apply' => $value],
    ]]));

    expect($config->isEscapeValue())->toBe($expected)
        ->and($config->isEscapeValueOnApply())->toBe($expected);
})->with([
    'unset variable' => [null, true],
    'empty string' => ['', true],
    'zero' => ['0', false],
    'false string' => ['false', false],
    'one' => ['1', true],
]);

it('supports fluent and closure value escaping settings independently of labels', function (): void {
    $config = moonshine()->getConfig();

    expect($config->escapeValue(false)->escapeValueOnApply(false))->toBe($config)
        ->and($config->isEscapeValue())->toBeFalse()
        ->and($config->isEscapeValueOnApply())->toBeFalse()
        ->and($config->escapeValue()->escapeValueOnApply())->toBe($config)
        ->and($config->isEscapeValue())->toBeTrue()
        ->and($config->isEscapeValueOnApply())->toBeTrue()
        ->and($config->isEscapeLabel())->toBeTrue();

    $config->set('escapes.value', static fn (): bool => false)
        ->set('escapes.value_on_apply', static fn (): bool => false);

    expect($config->isEscapeValue())->toBeFalse()
        ->and($config->isEscapeValueOnApply())->toBeFalse();
});

it('uses independent global defaults and field overrides when rendering and saving', function (
    string $fieldClass,
    ?bool $escape,
    ?bool $escapeOnApply,
    ?string $previewOverride,
    ?string $applyOverride,
    bool $escapedPreview,
    bool $escapedStoredValue,
): void {
    $value = '<b>Tom & "Jerry"</b>';
    $escaped = '&lt;b&gt;Tom &amp; &quot;Jerry&quot;&lt;/b&gt;';
    $field = $fieldClass::make('Name');

    if ($previewOverride !== null) {
        $field->{$previewOverride}();
    }

    if ($applyOverride === 'default') {
        $field->escapeOnApply();
    } elseif ($applyOverride !== null) {
        $field->escapeOnApply(function (Text|Textarea $context) use ($fieldClass, $applyOverride): bool {
            expect($context)->toBeInstanceOf($fieldClass)
                ->and($context->getColumn())->toBe('name');

            return $applyOverride === 'true';
        });
    }

    // Configure after creating the field to verify that defaults are resolved lazily.
    if ($escape !== null) {
        moonshine()->getConfig()->escapeValue($escape);
    }

    if ($escapeOnApply !== null) {
        moonshine()->getConfig()->escapeValueOnApply($escapeOnApply);
    }

    $preview = (string) (clone $field)->fill($value)->previewMode()->render();

    expect($preview)->toContain($escapedPreview ? $escaped : $value);

    if ($escapedPreview) {
        expect($preview)->not->toContain($value);
    }

    $user = MoonshineUser::query()->firstOrFail();
    $form = FormBuilder::make()
        ->fields([$field])
        ->fillCast($user, new ModelCaster(MoonshineUser::class));

    fakeRequest(method: 'post', parameters: ['name' => $value]);

    $form->apply(static fn (MoonshineUser $item): bool => $item->save(), throw: true);

    expect($user->refresh()->name)->toBe($escapedStoredValue ? $escaped : $value);
})->with([Text::class, Textarea::class, Email::class])->with([
    'default configuration' => [null, null, null, null, true, true],
    'both enabled' => [true, true, null, null, true, true],
    'both disabled' => [false, false, null, null, false, false],
    'only preview' => [true, false, null, null, true, false],
    'only saving' => [false, true, null, null, false, true],
    'field disables preview' => [true, true, 'unescape', null, false, true],
    'field enables preview' => [false, false, 'escape', null, true, false],
    'field disables saving' => [true, true, null, 'false', true, false],
    'field enables saving' => [false, false, null, 'true', false, true],
    'field enables saving without a condition' => [false, false, null, 'default', false, true],
    'both field overrides disable escaping' => [true, true, 'unescape', 'false', false, false],
    'both field overrides enable escaping' => [false, false, 'escape', 'default', true, true],
]);
