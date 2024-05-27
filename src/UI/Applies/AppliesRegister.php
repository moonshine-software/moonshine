<?php

declare(strict_types=1);

namespace MoonShine\UI\Applies;

use MoonShine\Core\Contracts\ApplyContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\Field;

final class AppliesRegister
{
    private string $type = 'fields';

    private string $for = ModelResource::class;

    private array $applies = [
        'filters' => [],
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

    public function findByField(
        Field $field,
        string $type = 'fields',
        string $for = ModelResource::class
    ): ?ApplyContract {
        if ($field->hasOnApply()) {
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
     */
    public function add(string $fieldClass, string $applyClass): AppliesRegister
    {
        $this->applies[$this->type][$this->for][$fieldClass] = $applyClass;

        return $this;
    }

    /**
     * @param  list<class-string<Field>, class-string<ApplyContract>>  $data
     */
    public function push(array $data): AppliesRegister
    {
        $this->applies[$this->type][$this->for] = array_merge(
            $this->applies[$this->type][$this->for] ?? [],
            $data,
        );

        return $this;
    }

    /**
     * @param  class-string<Field>  $fieldClass
     */
    public function get(string $fieldClass, ?ApplyContract $default = null): ?ApplyContract
    {
        $apply = $this->applies[$this->type][$this->for][$fieldClass] ?? null;

        if (is_null($apply)) {
            foreach ($this->applies[$this->type][$this->for] ?? [] as $fieldApply => $applyClass) {
                if (is_subclass_of($fieldClass, $fieldApply)) {
                    $apply = $applyClass;

                    break;
                }
            }
        }

        if (is_null($apply) && ! is_null($default)) {
            $apply = $default;
        }

        if (is_null($apply)) {
            return null;
        }

        return moonshine()->getContainer($apply);
    }
}
