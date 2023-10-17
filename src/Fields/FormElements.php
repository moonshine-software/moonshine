<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Contracts\Decorations\FieldsDecoration;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Decorations\Decoration;
use MoonShine\Exceptions\FieldsException;
use ReflectionClass;
use ReflectionException;
use Throwable;

/**
 * @extends MoonShineRenderElements<int, Field|Decoration>
 */
abstract class FormElements extends MoonShineRenderElements
{
    /**
     * @throws Throwable
     */
    public function onlyFields(): Fields
    {
        $data = [];

        $this->extractOnly($this->toArray(), Field::class, $data);

        return Fields::make($data);
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
     * @template T of Field
     * @param  class-string<T>  $class
     * @param ?Field  $default
     * @return T
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
