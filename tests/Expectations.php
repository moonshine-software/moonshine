<?php

use MoonShine\UI\Fields\Field;
use Pest\Expectation;

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

expect()->extend('hasFields', fn (array $fields = null) => expect($this->value)
    ->toBeCollection()
    ->toHaveCount($fields ? \count($fields) : 0));
