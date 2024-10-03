<?php

declare(strict_types=1);

use MoonShine\Support\ListOf;

uses()->group('support');

class SomeType
{
}
class SomeTypeSub1 extends SomeType
{
}
class SomeTypeSub2 extends SomeType
{
}
class SomeTypeSub3 extends SomeType
{
}
class SomeTypeSub4 extends SomeType
{
}

enum SomeEnum: int
{
    case ONE = 1;

    case TWO = 2;

    case THREE = 3;

    case FOUR = 4;

    case FIVE = 5;
}

beforeEach(function () {
    $this->listOfObjects = new ListOf(SomeType::class, [
        new SomeTypeSub1(),
        new SomeTypeSub2(),
        new SomeTypeSub3(),
    ]);

    $this->listOfEnum = new ListOf(SomeEnum::class, [
        SomeEnum::ONE,
        SomeEnum::TWO,
        SomeEnum::THREE,
    ]);
});

it('count of elements', function () {
    expect($this->listOfObjects->toArray())
        ->toHaveCount(3);
});

it('except elements', function () {
    expect($this->listOfEnum->except(SomeEnum::TWO, SomeEnum::THREE)->toArray())
        ->toHaveCount(1)
        ->not->toContain(SomeEnum::TWO, SomeEnum::THREE);
});

it('except type', function () {
    expect($this->listOfObjects->except(SomeTypeSub2::class, SomeTypeSub3::class)->toArray())
        ->toHaveCount(1)
        ->not->toContain(new SomeTypeSub2(), new SomeTypeSub3());
});

it('except closure', function () {
    expect($this->listOfObjects->except(fn ($item) => get_class($item) === SomeTypeSub2::class)->toArray())
        ->toHaveCount(2)
        ->not->toContain(new SomeTypeSub2());
});


it('except mixed', function () {
    expect($this->listOfObjects->except(fn ($item) => get_class($item) === SomeTypeSub2::class, SomeTypeSub3::class)->toArray())
        ->toHaveCount(1)
        ->not->toContain(new SomeTypeSub2(), new SomeTypeSub3());
});

it('add elements', function () {
    expect($this->listOfEnum->add(SomeEnum::FOUR, SomeEnum::FIVE)->toArray())
        ->toHaveCount(5);
});

it('prepend elements', function () {
    expect($this->listOfEnum->prepend(SomeEnum::FOUR)->toArray()[0])
        ->toBe(SomeEnum::FOUR);
});

it('add object elements', function () {
    expect($this->listOfObjects->add(new SomeTypeSub4())->toArray())
        ->toHaveCount(4);
});

it('ensure elements', function () {
    expect((new ListOf(SomeTypeSub2::class, [SomeEnum::ONE]))->toArray());
})->expectException(UnexpectedValueException::class);

it('only elements', function () {
    expect($this->listOfEnum->only(SomeEnum::ONE, SomeEnum::TWO)->toArray())
        ->toHaveCount(2)
        ->toContain(SomeEnum::ONE, SomeEnum::TWO)
        ->not->toContain(SomeEnum::THREE);
});

it('only type', function () {
    $listOf = $this->listOfObjects->only(SomeTypeSub1::class, SomeTypeSub2::class)->toArray();
    expect($listOf)
        ->toHaveCount(2)
        ->not->toContain(new SomeTypeSub3())
        ->and(collect($listOf)->every(fn ($item) => $item instanceof SomeTypeSub1 || $item instanceof SomeTypeSub2))
        ->toBeTrue();
});

it('only closure', function () {
    $listOf = $this->listOfObjects->only(fn ($item) => get_class($item) === SomeTypeSub1::class)->toArray();
    expect($listOf)
        ->toHaveCount(1)
        ->not->toContain(new SomeTypeSub2(), new SomeTypeSub3())
        ->and(collect($listOf)->every(fn ($item) => $item instanceof SomeTypeSub1))
        ->toBeTrue();
});

it('only mixed', function () {
    $listOf = $this->listOfObjects->only(fn ($item) => get_class($item) === SomeTypeSub1::class, SomeTypeSub3::class)->toArray();
    expect($listOf)
        ->toHaveCount(2)
        ->not->toContain(new SomeTypeSub2())
        ->and(collect($listOf)->every(fn ($item) => $item instanceof SomeTypeSub1 || $item instanceof SomeTypeSub3))
        ->toBeTrue();
});
