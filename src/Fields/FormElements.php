<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Collection;
use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Contracts\Fields\FieldsWrapper;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Decorations\Decoration;
use Throwable;

/**
 * @extends MoonShineRenderElements<int, Field|Decoration>
 */
abstract class FormElements extends MoonShineRenderElements
{
    protected bool $onlyFieldsCalled = false;

    public function onlyFieldsCalled(): static
    {
        $this->onlyFieldsCalled = true;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function onlyFields(bool $withWrappers = false): Fields
    {
        if($this->onlyFieldsCalled) {
            return Fields::make($this->toArray())
                ->onlyFieldsCalled();
        }

        $data = [];

        $this->extractFields($this->toArray(), $data);

        return Fields::make($data)->when(
            ! $withWrappers,
            fn (Fields $fields): Fields|FormElements => $fields->withoutWrappers()
        )->onlyFieldsCalled();
    }

    /**
     * @throws Throwable
     */
    public function prepareAttributes(): Fields
    {
        return $this->onlyFields()
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
        return $this->first(
            static fn (Field $field): bool => $field::class === $class,
            $default
        );
    }

    public function withoutWrappers(): FormElements|Fields
    {
        return $this->unwrapElements(FieldsWrapper::class);
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

    /**
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
     * @throws Throwable
     */
    public function whenFieldsConditions(): Fields
    {
        return $this->whenFields()->map(
            static fn (
                Field $field
            ): array => $field->showWhenCondition()
        );
    }
}
