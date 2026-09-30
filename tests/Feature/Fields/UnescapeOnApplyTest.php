<?php

declare(strict_types=1);

use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

uses()->group('fields');

it('preserves unescape persistence behavior and explicit apply overrides', function (
    string $fieldClass,
    array $operations,
    bool $escapedPreview,
    bool $escapedStoredValue,
): void {
    $value = '<svg viewBox="0 0 24 24"></svg>';
    $escaped = '&lt;svg viewBox=&quot;0 0 24 24&quot;&gt;&lt;/svg&gt;';
    $field = $fieldClass::make('Name');

    foreach ($operations as $operation) {
        match ($operation) {
            'apply:true' => $field->escapeOnApply(static fn (Text|Textarea $context): bool => $context->getColumn() === 'name'),
            'apply:false' => $field->escapeOnApply(static fn (Text|Textarea $context): bool => $context->getColumn() !== 'name'),
            'apply:default' => $field->escapeOnApply(),
            'apply:null' => $field->escapeOnApply(null),
            default => $field->{$operation}(),
        };
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
    'default escaping' => [[], true, true],
    'unescape also preserves submitted markup' => [['unescape'], false, false],
    'escape restores both defaults' => [['unescape', 'escape'], true, true],
    'explicit apply false preserves escaped preview' => [['apply:false'], true, false],
    'explicit apply true overrides unescape' => [['unescape', 'apply:true'], false, true],
    'later unescape respects explicit apply true' => [['apply:true', 'unescape'], false, true],
    'later escape respects explicit apply false' => [['apply:false', 'escape'], true, false],
    'apply without a condition overrides unescape' => [['unescape', 'apply:default'], false, true],
    'later unescape respects apply without a condition' => [['apply:default', 'unescape'], false, true],
    'explicit null enables apply escaping' => [['unescape', 'apply:null'], false, true],
    'last apply condition wins' => [['unescape', 'apply:true', 'apply:false'], false, false],
    'default apply resets an earlier false condition' => [['unescape', 'apply:false', 'apply:default'], false, true],
]);
