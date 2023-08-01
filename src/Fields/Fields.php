<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Exceptions\FieldException;
use Throwable;

/**
 * @template TKey of array-key
 * @template Field
 *
 * @extends  Collection<TKey, Field>
 */
final class Fields extends FormElements
{
    /**
     * @throws Throwable
     */
    public function fillValues(array $rawValues = [], mixed $castedValues = null): self
    {
        return $this->onlyFields()->map(fn (Field $field): Field => clone $field->resolveValue($rawValues, $castedValues));
    }

    public function requestValues(string $prefix = null): Fields
    {
        return $this->onlyFields()->mapWithKeys(fn(Field $field): array => [$field->column() => $field->requestValue($prefix)])->filter();
    }

    /**
     * @throws Throwable
     */
    public function resolveChildFields(Field $parent): Fields
    {
        return $this->map(function (Field $field) use ($parent): Field|NoInput {
            throw_if(
                $parent instanceof Json && $field->hasRelationship(),
                new FieldException(
                    'Relationship fields in JSON field unavailable. Use resourceMode'
                )
            );

            if ($parent instanceof HasPivot) {
                return $field->setName(
                    "{$parent->relation()}_{$field->column()}[]"
                );
            }

            if ($field instanceof HasFields
                && $field->hasRelationship()
                && $field->isNowOnForm()
                && ! $parent->isResourceModeField()) {
                return NoInput::make(
                    $field->label(),
                    $field->column(),
                    static fn (): string => 'Relationship fields with fields unavailable. Use resourceMode'
                )->badge('red');
            }

            return $field->setName(
                (string) str($parent->name())
                    ->when(
                        $parent->hasFields(),
                        fn (Stringable $s): Stringable => $s->append(
                            '[${index' . $s->substrCount('$') . '}]'
                        )
                    )
                    ->append("[{$field->column()}]")
                    ->replace('[]', '')
                    ->when(
                        $field->getAttribute('multiple'),
                        static fn (Stringable $s): Stringable => $s->append(
                            '[]'
                        )
                    )
            )->xModel();
        });
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function indexFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnIndex())
            ->values();
    }


    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function relatable(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => $field->isResourceModeField()
            )
            ->values()
            ->map(fn (Field $field): Field => $field->setParents());
    }

    /**
     * @throws Throwable
     */
    public function withoutCanBeRelatable(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => ! $field->canBeResourceMode()
            )
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function withoutRelatable(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => ! $field->isResourceModeField()
            )
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function formFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnForm())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function detailFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnDetail())
            ->values();
    }

    /**
     * @return Fields<File>
     *
     * @throws Throwable
     */
    public function onlyDeletableFileFields(bool $isDeleteFiles = true): Fields
    {
        return $this->onlyFileFields()
            ->filter(
                static fn (Field $field): bool => $field->isDeleteFiles() === $isDeleteFiles
            )
            ->values();
    }

    /**
     * @return Fields<File>
     *
     * @throws Throwable
     */
    public function onlyFileFields(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => $field instanceof Fileable
            )
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function exportFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnExport())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function importFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnImport())
            ->values();
    }
}
