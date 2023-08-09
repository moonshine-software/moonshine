<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Decorations\Decoration;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\FormElement;
use MoonShine\Fields\StackFields;
use Throwable;

/**
 * @mixin MoonShineRenderable
 */
trait WithFields
{
    protected array $fields = [];

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        if ($this instanceof FormElement
            && $this instanceof HasFields
            && ! $this instanceof HasPivot
            && ! $this->hasFields()
            && $this->getResource()
        ) {
            $this->fields(
                $this->getResource()
                    ->getFields()
                    ->onlyFields()
                    ->unwrapFields(StackFields::class)
                    ->toArray() ?? []
            );
        }

        return Fields::make($this->fields)->when(
            $this instanceof HasFields && ! $this instanceof Decoration,
            fn (Fields $fields): Fields => $fields->resolveSiblings($this)
        );
    }

    public function hasFields(): bool
    {
        return count($this->fields) > 0;
    }

    /**
     * @return $this
     */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }
}
