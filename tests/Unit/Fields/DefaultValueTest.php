<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Json;
use MoonShine\Fields\Number;
use MoonShine\Fields\Select;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Enums\TestEnumColor;

uses()->group('default-value');

beforeEach(function () {
    $this->item = new class extends Model
    {
        public mixed $value;
    };
});

expect()->extend('toBeDefaultWith', function (mixed $default) {
    $item = new class extends Model
    {
        public mixed $value;
    };

    return expect($this->value
        ->default($default)
        ->formViewValue($item)
    )->toBe($default);
});

dataset('all_values', [
    null,
    'string',
    [1,2,3],
    2,
    true,
    false,
    TestEnumColor::Red,
    new MoonshineUser()
]);

it('text default values', function ($default) {
    $field = Text::make('Value');

    expect($field)->when(
        $default === 'string',
        fn($field) => $field->toBeDefaultWith($default),
        fn($field) => $field->not->toBeDefaultWith($default)
    );
})->with('all_values');

it('enum default values', function ($default) {
    $field = Enum::make('Value')
        ->attach(TestEnumColor::class);

    expect($field)->when(
        $default === TestEnumColor::Red,
        fn($field) => $field->toBeDefaultWith($default),
        fn($field) => $field->not->toBeDefaultWith($default)
    );
})->with('all_values');

it('enum default values is selected', function () {
    $field = Enum::make('Value')
        ->attach(TestEnumColor::class)
        ->default("R");

    expect($field->isSelected($this->item, "R"))
        ->toBeTrue()
        ->and($field->isSelected($this->item, "B"))
        ->toBeFalse()
        ->and($field->isSelected($this->item, "W"))
        ->toBeFalse();

    $field = Enum::make('Value')
        ->attach(TestEnumColor::class)
        ->default(TestEnumColor::Red);

    expect($field->isSelected($this->item, "R"))
        ->toBeTrue()
        ->and($field->isSelected($this->item, "B"))
        ->toBeFalse()
        ->and($field->isSelected($this->item, "W"))
        ->toBeFalse();
});

it('enum default values is selected multiple', function () {
    $field = Enum::make('Value')
        ->attach(TestEnumColor::class)
        ->multiple()
        ->default(["B", "R"]);

    expect($field->isSelected($this->item, "R"))
        ->toBeTrue()
        ->and($field->isSelected($this->item, "B"))
        ->toBeTrue()
        ->and($field->isSelected($this->item, "W"))
        ->toBeFalse();
});

it('checkbox default values', function ($default) {
    $field = Checkbox::make('Value');

    expect($field)->when(
        in_array($default, ['string', true, false, 2], true),
        fn($field) => $field->toBeDefaultWith($default),
        fn($field) => $field->not->toBeDefaultWith($default)
    );
})->with('all_values');


it('checkbox default values is checked', function () {
    $field = Checkbox::make('Value')
        ->default("Test");

    expect($field->isChecked($this->item, "Test"))
        ->toBeTrue()
        ->and($field->isChecked($this->item, "B"))
        ->toBeFalse()
        ->and($field->isChecked($this->item, "W"))
        ->toBeFalse();

    $field = Checkbox::make('Value')
        ->default(true);

    expect($field->isChecked($this->item, true))
        ->toBeTrue()
        ->and($field->isChecked($this->item, false))
        ->toBeFalse();
});

it('number default values', function ($default) {
    $field = Number::make('Value');

    expect($field)->when(
        in_array($default, ['string', 2, null], true),
        fn($field) => $field->toBeDefaultWith($default),
        fn($field) => $field->not->toBeDefaultWith($default)
    );
})->with('all_values');

it('switcher default values', function ($default) {
    $field = SwitchBoolean::make('Value');

    expect($field)->when(
        in_array($default, ['string', true, false, 2], true),
        fn($field) => $field->toBeDefaultWith($default),
        fn($field) => $field->not->toBeDefaultWith($default)
    );
})->with('all_values');


it('json default values', function ($default) {
    $field = Json::make('Value');

    expect($field)->when(
        is_array($default),
        fn($field) => $field->toBeDefaultWith($default),
        fn($field) => $field->not->toBeDefaultWith($default)
    );
})->with('all_values');

it('select default values', function ($default) {
    $field = Select::make('Value');

    expect($field)->when(
        in_array($default, ['string', [1,2,3], 2], true),
        fn($field) => $field->toBeDefaultWith($default),
        fn($field) => $field->not->toBeDefaultWith($default)
    );
})->with('all_values');

it('select default values is selected', function () {
    $field = Select::make('Value')
        ->default("R");

    expect($field->isSelected($this->item, "R"))
        ->toBeTrue()
        ->and($field->isSelected($this->item, "B"))
        ->toBeFalse()
        ->and($field->isSelected($this->item, "W"))
        ->toBeFalse();
});

it('select default values is selected multiple', function () {
    $field = Select::make('Value')
        ->multiple()
        ->default(["B", "R"]);

    expect($field->isSelected($this->item, "R"))
        ->toBeTrue()
        ->and($field->isSelected($this->item, "B"))
        ->toBeTrue()
        ->and($field->isSelected($this->item, "W"))
        ->toBeFalse();
});


