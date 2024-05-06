<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Item;

uses()->group('core');
uses()->group('applies');

it('add new field apply', function (): void {
    appliesRegister()
        ->type('fields')
        ->for(ModelResource::class)
        ->add(Text::class, CustomTextFieldApply::class);

    $field = Text::make('Column');

    $this->post('/', ['column' => '!']);

    $data = $field->apply(fn (array $data) => $data, [
        'column' => 'hello',
    ]);

    expect($data)
        ->toBe(['column' => 'hello world!']);
});

it('add new filter apply', function (): void {
    appliesRegister()
        ->type('filters')
        ->for(ModelResource::class)
        ->add(Text::class, CustomTextFilterApply::class);

    $field = Text::make('Column');

    $this->post('/', ['column' => '!']);

    $filterApply = appliesRegister()->findByField($field, 'filters');

    $defaultApply = static fn (Builder $query): Builder => $query->where(
        $field->getColumn(),
        $field->requestValue()
    );

    if(! is_null($filterApply)) {
        $field->onApply($filterApply->apply($field));
    }

    $data = $field->apply($defaultApply, (new Item())->newQuery());

    expect($data->toRawSql())
        ->toContain("`some_column` = '!'");
});

class CustomTextFilterApply implements ApplyContract
{
    public function apply(Field $field): Closure
    {
        return static function (Builder $q, string $value, Field $field) {
            return $q->where('some_column', $value);
        };
    }
}

class CustomTextFieldApply implements ApplyContract
{
    public function apply(Field $field): Closure
    {
        return static function (array $data, string $value, Field $field) {
            $data[$field->getColumn()] .= ' world' . $value;

            return $data;
        };
    }
}
