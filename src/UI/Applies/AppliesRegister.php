<?php

declare(strict_types=1);

namespace MoonShine\UI\Applies;

use MoonShine\Contracts\ApplyContract;
use MoonShine\Core\Resources\Resource;
use MoonShine\UI\Fields\Field;

final class AppliesRegister
{
    private string $type = 'fields';

    private ?string $for = null;

    private string $defaultFor = Resource::class;

    private array $applies = [
        'filters' => [],
        'fields' => [],
    ];

    public function type(string $type): AppliesRegister
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param  class-string  $for
     */
    public function for(string $for): AppliesRegister
    {
        $this->for = $for;

        return $this;
    }

    /**
     * @return class-string
     */
    public function getFor(): string
    {
        return $this->for ?? $this->getDefaultFor();
    }

    /**
     * @param  class-string  $for
     */
    public function defaultFor(string $for): AppliesRegister
    {
        $this->defaultFor = $for;

        return $this;
    }

    /**
     * @return class-string
     */
    public function getDefaultFor(): string
    {
        return $this->defaultFor;
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
        ?string $for = null
    ): ?ApplyContract {
        if ($field->hasOnApply()) {
            return null;
        }

        return appliesRegister()
            ->type($type)
            ->for($for ?? $this->getDefaultFor())
            ->get($field::class);
    }

    /**
     * @param  class-string<Field>  $fieldClass
     * @param  class-string<ApplyContract>  $applyClass
     */
    public function add(string $fieldClass, string $applyClass): AppliesRegister
    {
        $this->applies[$this->type][$this->getFor()][$fieldClass] = $applyClass;

        return $this;
    }

    /**
     * @param  list<class-string<Field>, class-string<ApplyContract>>  $data
     */
    public function push(array $data): AppliesRegister
    {
        $this->applies[$this->type][$this->getFor()] = array_merge(
            $this->applies[$this->type][$this->getFor()] ?? [],
            $data,
        );

        return $this;
    }

    /**
     * @param  class-string<Field>  $fieldClass
     */
    public function get(string $fieldClass, ?ApplyContract $default = null): ?ApplyContract
    {
        $apply = $this->applies[$this->type][$this->getFor()][$fieldClass] ?? null;

        if (is_null($apply)) {
            foreach ($this->applies[$this->type][$this->getFor()] ?? [] as $fieldApply => $applyClass) {
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
