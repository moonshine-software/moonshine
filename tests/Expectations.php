<?php

use MoonShine\UI\Fields\Field;
use Pest\Expectation;

expect()->extend('storeAvatarFile', static function ($avatar, $field, $item): Expectation {

    fakeRequest(method: 'POST', parameters: [
        'avatar' => $avatar,
    ]);

    $field->save($item);

    return expect($item->avatar)
        ->toBe('files/' . $avatar->hashName());
});

expect()->extend('applies', static function (Field $field): Expectation {
    return expect($field->onApply(static fn ($data) => ['onApply'])->apply(static fn ($data) => $data, []))
        ->toBe(['onApply'])
        ->and($field->onBeforeApply(static fn ($data) => ['onBeforeApply'])->beforeApply([]))
        ->toBe(['onBeforeApply'])
        ->and($field->onAfterApply(static fn ($data) => ['onAfterApply'])->afterApply([]))
        ->toBe(['onAfterApply'])
        ->and($field->onAfterDestroy(static fn ($data) => ['onAfterDestroy'])->afterDestroy([]))
        ->toBe(['onAfterDestroy']);
});
