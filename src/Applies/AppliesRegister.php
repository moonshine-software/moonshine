<?php

declare(strict_types=1);

namespace MoonShine\Applies;

use MoonShine\Applies\Filters\BelongsToManyModelApply;
use MoonShine\Applies\Filters\CheckboxModelApply;
use MoonShine\Applies\Filters\DateModelApply;
use MoonShine\Applies\Filters\DateRangeModelApply;
use MoonShine\Applies\Filters\JsonModelApply;
use MoonShine\Applies\Filters\MorphToModelApply;
use MoonShine\Applies\Filters\RangeModelApply;
use MoonShine\Applies\Filters\SelectModelApply;
use MoonShine\Applies\Filters\TextModelApply;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Date;
use MoonShine\Fields\DateRange;
use MoonShine\Fields\Field;
use MoonShine\Fields\Json;
use MoonShine\Fields\Range;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Resources\ModelResource;

final class AppliesRegister
{
    private string $type = 'fields';

    private string $for = ModelResource::class;

    private array $applies = [
        'filters' => [
            ModelResource::class => [
                Date::class => DateModelApply::class,
                Range::class => RangeModelApply::class,
                DateRange::class => DateRangeModelApply::class,
                BelongsToMany::class => BelongsToManyModelApply::class,
                MorphTo::class => MorphToModelApply::class,
                Json::class => JsonModelApply::class,
                Text::class => TextModelApply::class,
                Textarea::class => TextModelApply::class,
                Checkbox::class => CheckboxModelApply::class,
                Select::class => SelectModelApply::class,
            ],
        ],
        'fields' => [],
    ];

    public function type(string $type): AppliesRegister
    {
        $this->type = $type;

        return $this;
    }

    public function for(string $for): AppliesRegister
    {
        $this->for = $for;

        return $this;
    }

    public function filters(): AppliesRegister
    {
        $this->type('filters');

        return $this;
    }

    public function fields(): AppliesRegister
    {
        $this->type('fields');

        return $this;
    }

    public function findByField(Field $field, string $type = 'fields', string $for = ModelResource::class): ?ApplyContract
    {
        if($field->hasOnApply()) {
            return null;
        }

        return appliesRegister()
            ->type($type)
            ->for($for)
            ->get($field::class);
    }

    /**
     * @param  class-string<Field>  $fieldClass
     * @param  class-string<ApplyContract>  $applyClass
     * @return $this
     */
    public function add(string $fieldClass, string $applyClass): AppliesRegister
    {
        $this->applies[$this->type][$this->for][$fieldClass] = $applyClass;

        return $this;
    }

    /**
     * @param  class-string<Field>  $fieldClass
     */
    public function get(string $fieldClass, ?ApplyContract $default = null): ?ApplyContract
    {
        $apply = $this->applies[$this->type][$this->for][$fieldClass] ?? $default;

        if(is_null($apply)) {
            return null;
        }

        return app($apply);
    }
}
