<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Decorations\Decoration;
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
    public function resolveChildFields(Field $parent): Fields
    {
        return $this->map(function (Field $field) use ($parent) {
            throw_if(
                $parent instanceof Json && $field->hasRelationship(),
                new FieldException('Relationship fields in JSON field unavailable. Use resourceMode')
            );

            if ($parent instanceof HasPivot) {
                return $field->setName("{$parent->relation()}_{$field->field()}[]");
            }

            if ($field instanceof HasFields
                && $field->hasRelationship()
                && $field->isNowOnForm()
                && !$parent->isResourceModeField()) {
                return NoInput::make(
                    $field->label(),
                    $field->field(),
                    static fn() => 'Relationship fields with fields unavailable. Use resourceMode'
                )->badge('red');
            }

            return $field->setName(
                (string)str($parent->name())
                    ->when(
                        $parent->hasFields(),
                        fn(Stringable $s) => $s->append('[${index'.$s->substrCount('$').'}]')
                    )
                    ->append("[{$field->field()}]")
                    ->replace('[]', '')
                    ->when(
                        $field->getAttribute('multiple'),
                        static fn(Stringable $s) => $s->append('[]')
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
            ->filter(static fn(Field $field) => $field->isOnIndex())
            ->values();
    }


    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function relatable(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn(Field $field) => $field->isResourceModeField())
            ->values()
            ->map(fn(Field $field) => $field->setParents());
    }

    /**
     * @throws Throwable
     */
    public function withoutCanBeRelatable(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn(Field $field) => !$field->canBeResourceMode())
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function withoutRelatable(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn(Field $field) => !$field->isResourceModeField())
            ->values();
    }

    /**
     * @return Fields<Field|Decoration>
     * @throws Throwable
     */
    public function formFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn(Field $field) => $field->isOnForm())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function detailFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn(Field $field) => $field->isOnDetail())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function exportFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn(Field $field) => $field->isOnExport())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function importFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn(Field $field) => $field->isOnImport())
            ->values();
    }
}
