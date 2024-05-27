<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use MoonShine\Core\Contracts\MoonShineRenderable;
use MoonShine\UI\Collections\Fields;
use Throwable;

/**
 * @mixin MoonShineRenderable
 */
trait WithFields
{
    protected array|Closure $fields = [];

    /**
     * @throws Throwable
     */
    public function preparedFields(): Fields
    {
        return $this->getFields();
    }

    /**
     * @throws Throwable
     * // TODO make generic
     */
    public function getFields(): Fields
    {
        return fields(
            $this->getRawFields()
        );
    }

    public function getRawFields(): array
    {
        return value($this->fields, $this);
    }

    /**
     * @throws Throwable
     */
    public function hasFields(): bool
    {
        return $this->getFields()->isNotEmpty();
    }

    public function fields(Fields|Closure|array $fields): static
    {
        if($fields instanceof Closure) {
            $fields = $fields();
        }

        if(moonshine()->runningInConsole()) {
            $fields = collect($fields)
                ->map(fn (object $field): object => clone $field)
                ->toArray();
        }

        $this->fields = $fields instanceof Fields
            ? $fields->toArray()
            : $fields;

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function getFilledFields(
        array $raw = [],
        mixed $casted = null,
        int $index = 0,
        ?Fields $preparedFields = null
    ): Fields {
        $fields = $preparedFields ?? $this->getFields();

        return $fields->fillCloned($raw, $casted, $index, $fields);
    }
}
