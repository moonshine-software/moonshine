<?php

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Contracts\Fields\FieldWithPivotContract;
use Leeto\MoonShine\Exceptions\FieldException;
use Leeto\MoonShine\Fields\HasMany;
use Throwable;

trait FieldWithFieldsTrait
{
    protected array $fields = [];

    public function getFields(): array
    {
        return $this->fields;
    }

    #[Pure]
    public function hasFields(): bool
    {
        return isset($this->fields) && count($this->getFields());
    }

    /**
     * @throws Throwable
     */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        foreach ($fields as $field) {
            throw_if(
                $field instanceof HasMany,
                new FieldException('HasMany in fields unavailable now')
            );

            $field = $field->setParent($this)
                ->setId("{$this->relation()}_{$field->name()}");

            if($this instanceof FieldHasRelationContract) {
                if(!$this->isRelationHasOne() && !$this->isRelationToOne()) {
                    $field = $field->multiple();
                }

                if(!$this instanceof FieldWithPivotContract) {
                    $field = $field->xModel();
                }

                if($this->isRelationHasOne()) {
                    $field->setName("{$this->relation()}[{$field->field()}]");
                } elseif(!$this->isRelationToOne() && !$this instanceof FieldWithPivotContract) {
                    $field->setName("{$this->relation()}[\${index}][{$field->field()}]");
                } else {
                   $field->setName($this->relation() ? "{$this->relation()}_{$field->name()}" : $field->name());
                }
            } else {
                $field->xModel()
                    ->multiple()
                    ->setId("{$this->field()}_{$field->name()}")
                    ->setName("{$this->field()}[\${index}][{$field->field()}]");
            }
        }

        return $this;

    }

    public function jsonValues(Model $item = null): array
    {
        if(is_null($item)) {
            $data = ['id' => ''];

            foreach ($this->getFields() as $field) {
                $data[$field->field()] = '';
            }

            return $data;
        }

        if(isset($this->keyValue) && $this->isKeyValue()) {
            return collect($this->formViewValue($item))
                ->map(fn($value, $key) => ['key' => $key, 'value' => $value])
                ->values()
                ->toArray();
        }

        if($this->formViewValue($item) instanceof Collection) {
            return $this->formViewValue($item)->toArray();
        }

        if($this->formViewValue($item) instanceof Model) {
            return [$this->formViewValue($item)->toArray()];
        }

        return $this->formViewValue($item) ?? [];
    }
}