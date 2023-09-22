<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Contracts\Decorations\FieldsDecoration;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Exceptions\FieldsException;
use MoonShine\Fields\Relationships\ModelRelationField;
use ReflectionClass;
use ReflectionException;
use Throwable;

abstract class FormElements extends MoonShineRenderElements
{
    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function onlyFields(): Fields
    {
        $data = [];

        $this->extractOnly($this->toArray(), Field::class, $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     */
    public function prepareAttributes(): Fields
    {
        return $this->onlyFields()
            ->unwrapElements(StackFields::class)
            ->map(
                static function (Field $formElement): Field {
                    $formElement->when(
                        ! $formElement instanceof Fileable,
                        function ($field): void {
                            $field->customAttributes(
                                ['x-on:change' => 'onChangeField($event)']
                            );
                        }
                    );

                    return $formElement;
                }
            );
    }

    /**
     * @throws Throwable
     */
    public function whenFieldsConditions(): Fields
    {
        return $this->onlyFields()
            ->unwrapElements(StackFields::class)
            ->filter(
                static fn (Field $field): bool => $field->hasShowWhen()
            )
            ->map(
                static fn (
                    Field $field
                ): array => $field->showWhenCondition()
            );
    }

    /**
     * @throws Throwable
     */
    public function isWhenConditionField(string $name): bool
    {
        return $this->whenFieldNames()->has($name);
    }

    /**
     * @throws Throwable
     */
    public function whenFieldNames(): Fields
    {
        return $this->whenFields()->mapWithKeys(
            static fn (Field $field): array => [
                $field->showWhenCondition()['changeField'] => $field->showWhenCondition()['changeField'],
            ]
        );
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function whenFields(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => $field->hasShowWhen()
            )
            ->values();
    }

    /**
     * @return array<string, string>
     * @throws Throwable
     */
    public function extractLabels(): array
    {
        return $this->onlyFields()->flatMap(
            static fn (Field $field): array => [$field->column() => $field->label()]
        )->toArray();
    }


    /**
     * @throws Throwable
     */
    public function findByResourceClass(
        string $resource,
        ModelRelationField $default = null
    ): ?ModelRelationField {
        return $this->onlyRelationFields()->first(
            static fn (ModelRelationField $field): bool => $field->getResource()::class === $resource,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findByRelation(
        string $relation,
        ModelRelationField $default = null
    ): ?ModelRelationField {
        return $this->onlyRelationFields()->first(
            static fn (ModelRelationField $field): bool => $field->getRelationName() === $relation,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findByColumn(
        string $column,
        Field $default = null
    ): ?Field {
        return $this->onlyFields()->first(
            static fn (Field $field): bool => $field->column() === $column,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findByClass(
        string $class,
        Field $default = null
    ): ?Field {
        return $this->onlyFields()->first(
            static fn (Field $field): bool => $field::class === $class,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function onlyColumns(): Fields
    {
        return $this->onlyFields()->transform(
            static fn (Field $field): string => $field->column()
        );
    }

    /**
     * @throws ReflectionException|FieldsException
     */
    public function wrapIntoDecoration(
        string $class,
        Closure|string $label
    ): FormElements {
        $reflectionClass = new ReflectionClass($class);

        if (! $reflectionClass->implementsInterface(FieldsDecoration::class)) {
            throw FieldsException::wrapError();
        }

        return self::make([new $class($label, $this->toArray())]);
    }

    public function unwrapElements(string $class): FormElements
    {
        $modified = self::make();

        $this->each(
            static function ($element) use ($class, $modified): void {
                if ($element instanceof $class) {
                    $element->getFields()->each(
                        fn ($inner): Collection => $modified->push($inner)
                    );
                } else {
                    $modified->push($element);
                }
            }
        );

        return $modified;
    }
}
