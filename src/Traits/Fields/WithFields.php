<?php

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Contracts\Fields\HasPivotContract;
use Leeto\MoonShine\Exceptions\FieldException;
use Leeto\MoonShine\Fields\Json;
use Leeto\MoonShine\Fields\Text;
use Throwable;

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
            throw_if(
                $this instanceof Json && $field->hasRelationship(),
                new FieldException('Relationship fields in JSON field unavailable now. Coming soon')
            );

            throw_if(
                $field->hasFields(),
                new FieldException('Field with fields unavailable now. Coming soon')
            );

            if ($this instanceof HasPivotContract) {
                return $field->setName("{$this->relation()}_{$field->field()}[]");
            }

            return $field->setName(
                str($this->name())
                    ->when(
                        $this->hasFields() && !$this->toOne(),
                        fn(Stringable $s) => $s->append('[${index'.$s->substrCount('$').'}]')
                    )
                    ->append("[{$field->field()}]")
                    ->replace('[]', '')
            )->xModel();
        })->toArray();
    }

    public function hasFields(): bool
    {
        return count($this->fields);
    }

    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function jsonValues(Model $item = null): array
    {
        if (is_null($item)) {
            $data = ['id' => ''];

            foreach ($this->getFields() as $field) {
                $data[$field->field()] = '';
            }

            return $data;
        }

        if ($this instanceof Json && $this->isKeyValue()) {
            return collect($this->formViewValue($item))
                ->map(fn($value, $key) => ['key' => $key, 'value' => $value])
                ->values()
                ->toArray();
        }

        if ($this->formViewValue($item) instanceof Collection) {
            return $this->formViewValue($item)->toArray();
        }

        if ($this->formViewValue($item) instanceof Model) {
            return [$this->formViewValue($item)->toArray()];
        }

        return $this->formViewValue($item) ?? [];
    }
}
