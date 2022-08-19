<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Support\Stringable;
use Leeto\MoonShine\Contracts\Fields\HasPivot;

trait WithFields
{
    protected array $fields = [];

    protected bool $fullPage = false;

    public function fullPage(): static
    {
        $this->fullPage = true;

        return $this;
    }

    public function isFullPage(): bool
    {
        return $this->fullPage;
    }

    public function getFields(): array
    {
        return collect($this->fields)->map(function ($field) {
            if ($this instanceof HasPivot) {
                return $field->setName("{$this->relation()}_{$field->field()}[]");
            }

            return $field
                ->setName(
                    (string) str($this->name())
                        ->when(
                            $this->hasFields() && !$this->toOne(),
                            fn(Stringable $s) => $s->append('[${index'.$s->substrCount('$').'}]')
                        )
                        ->append("[{$field->field()}]")
                        ->replace('[]', '')
                );
        })->toArray();
    }

    public function hasFields(): bool
    {
        return !empty($this->fields);
    }

    /**
     * @param  array  $fields
     * @return $this
     */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }
}
