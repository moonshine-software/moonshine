<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Fields\Text;

uses()->group('type-casts');

function countingModel(array $attributes): Model
{
    $model = new class () extends Model {
        public int $serializations = 0;

        protected $guarded = [];

        public function toArray(): array
        {
            $this->serializations++;

            return parent::toArray();
        }
    };

    return $model->forceFill($attributes);
}

it('serializes the model once for all filled fields', function (): void {
    $model = countingModel(['title' => 'Title', 'slug' => 'slug', 'content' => 'Content']);
    $casted = new ModelDataWrapper($model);

    $fields = Fields::make([
        Text::make('Title'),
        Text::make('Slug'),
        Text::make('Content'),
    ])->fillCloned($casted->toArray(), $casted);

    expect($fields->map(static fn (Text $field): mixed => $field->toValue())->all())
        ->toBe(['Title', 'slug', 'Content'])
        ->and($model->serializations)
        ->toBe(1);
});

it('serializes the model again after it is changed through the wrapper', function (): void {
    $model = countingModel(['title' => 'Before']);
    $casted = new ModelDataWrapper($model);

    expect($casted->toArray())->toBe(['title' => 'Before']);

    $casted->title = 'After';

    expect($casted->toArray())
        ->toBe(['title' => 'After'])
        ->and($model->serializations)
        ->toBe(2);
});
