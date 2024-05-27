<?php

use MoonShine\UI\Fields\Field;
use Pest\Expectation;

expect()->extend('storeAvatarFile', function ($avatar, $field, $item): Expectation {

    fakeRequest(method: 'POST', parameters: [
        'avatar' => $avatar,
    ]);

    $field->save($item);

    return expect($item->avatar)
        ->toBe('files/' . $avatar->hashName());
});

expect()->extend('applies', function (Field $field): Expectation {
    return expect($field->onApply(fn ($data) => ['onApply'])->apply(fn ($data) => $data, []))
        ->toBe(['onApply'])
        ->and($field->onBeforeApply(fn ($data) => ['onBeforeApply'])->beforeApply([]))
        ->toBe(['onBeforeApply'])
        ->and($field->onAfterApply(fn ($data) => ['onAfterApply'])->afterApply([]))
        ->toBe(['onAfterApply'])
        ->and($field->onAfterDestroy(fn ($data) => ['onAfterDestroy'])->afterDestroy([]))
        ->toBe(['onAfterDestroy']);
});
