<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Contracts\Fields\HasValueExtraction;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use MoonShine\Fields\Fields;
use MoonShine\Fields\FormElement;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;
use Throwable;

/**
 * @mixin FormElement
 */
trait WithFields
{
    protected array $fields = [];

    public function getFields(): Fields
    {
        $resolveChildFields = $this instanceof HasJsonValues || $this instanceof HasPivot
            || ($this instanceof HasResourceMode && ! $this->isResourceMode());

        if ($this instanceof HasFields && ! $this instanceof HasPivot && ! $this->hasFields()) {
            $this->fields(
                $this->resource()?->getFields()->withoutCanBeRelatable()?->toArray() ?? []
            );
        }

        return Fields::make($this->fields)->when(
            $resolveChildFields,
            fn (Fields $fields) => $fields->resolveChildFields($this)
        );
    }

    public function hasFields(): bool
    {
        return count($this->fields) > 0;
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

    /**
     * @throws Throwable
     */
    public function indexViewValue(Model $item, bool $container = false): string
    {
        $value = $item->{$this instanceof HasRelationship ? $this->relation() : $this->field()};

        if ($value instanceof Model) {
            $value = [$value];
        }

        if (method_exists($this, 'onlyCount') && $this->onlyCount) {
            return (string)$value->count();
        }

        $values = [];
        $fields = $this->getFields()
            ->indexFields()
            ->when($this instanceof HasPivot, function (Fields $field) use ($item) {
                return $field->prepend(
                    ID::make(
                        '#',
                        $item->{$this->relation()}()->getRelatedPivotKeyName()
                    )
                );
            });

        $columns = $fields->extractLabels();

        try {
            if (is_iterable($value)) {
                foreach ($value as $index => $data) {
                    if ($this instanceof HasPivot && $fields->isNotEmpty()) {
                        $pivotAs = $this->getPivotAs($data);

                        $data = tap($data->{$pivotAs}, function ($in) use ($data) {
                            $in->category_id = $data->{$this->resourceTitleField()};
                        });
                    }

                    if ($this instanceof Json && $this->isKeyValue()) {
                        $data = $this->extractValues([$index => $data]);
                    }

                    if (!$data instanceof Model) {
                        $fields->each(function ($field) use (&$data) {
                            if ($field instanceof HasValueExtraction && !$field instanceof Json) {
                                $data = array_merge($data, $field->extractValues($data[$field->field()]));
                            }
                        });

                        $data = (new class () extends Model {
                            protected $guarded = [];
                        })->newInstance($data);
                    }

                    foreach ($fields as $field) {
                        $values[$index][$field->field()] = $field->indexViewValue($data, false);
                    }
                }
            }
        } catch (Throwable $e) {
            report($e);
            $values = [];
        }

        return view('moonshine::ui.table', [
            'columns' => $columns,
            'values' => $values,
        ])->render();
    }
}
