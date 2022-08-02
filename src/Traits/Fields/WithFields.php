<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Leeto\MoonShine\Contracts\Fields\HasPivot;
use Leeto\MoonShine\Fields\Json;

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

    public function jsonSerialize(): array
    {
        if (!$this->value()) {
            $data = ['id' => ''];

            foreach ($this->getFields() as $field) {
                $data[$field->field()] = '';
            }

            return $data;
        }

        if ($this instanceof Json && $this->isKeyValue()) {
            return collect($this->value())
                ->map(fn($value, $key) => ['key' => $key, 'value' => $value])
                ->values()
                ->toArray();
        }

        if ($this->value() instanceof Collection) {
            return $this->value()->toArray();
        }

        if ($this->value() instanceof Model) {
            return [$this->value()->toArray()];
        }

        return $this->value() ?? [];
    }
}
