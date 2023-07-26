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
use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\FormElement;
use MoonShine\Fields\HasOne;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;
use MoonShine\Fields\StackFields;
use MoonShine\Filters\Filter;
use Throwable;

/**
 * @mixin ResourceRenderable
 */
trait WithFields
{
    protected array $fields = [];

    protected bool $onlyCount = false;

    protected bool $inLine = false;

    protected string $inLineSeparator = '';

    protected bool $inLineBadge = false;

    public function onlyCount(): static
    {
        $this->onlyCount = true;

        return $this;
    }

    public function inLine(string $separator = '', bool $badge = false): static
    {
        $this->inLine = true;
        $this->inLineSeparator = $separator;
        $this->inLineBadge = $badge;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function indexViewValue(Model $item, bool $container = true): string
    {
        if (! $this instanceof FormElement) {
            return '';
        }

        $value = $item->{$this instanceof HasRelationship ? $this->relation()
            : $this->field()};


        if ($value instanceof Model) {
            $value = [$value];
        }

        if ($this->onlyCount && ! $this instanceof HasOne) {
            return (string) ($this instanceof HasRelationship
                ? $value->count()
                : count($value));
        }

        if ($this instanceof HasRelationship
            && ! $this instanceof HasOne
            && $this->inLine
        ) {
            return $value?->implode(function ($item) use ($container) {
                $implodeValue = $item->{$this->resourceTitleField()} ?? false;

                if ($this->inLineBadge) {
                    $link = tryOrReturn(
                        fn () => $this->resource()?->route('show', $item->getKey()),
                        '',
                    );

                    return $container ? view('moonshine::ui.badge', [
                        'color' => 'purple',
                        'link' => $link,
                        'value' => $implodeValue,
                        'margin' => true,
                    ])->render() : $implodeValue;
                }

                return $implodeValue;
            }, $this->inLineSeparator) ?? '';
        }

        $values = [];
        $fields = $this->getFields()
            ->indexFields()
            ->when(
                $this instanceof HasRelationship && $this instanceof HasPivot,
                fn (Fields $field): Fields => $field->prepend(
                    ID::make(
                        '#',
                        $item->{$this->relation()}()->getRelatedPivotKeyName()
                    )
                )
            );

        $columns = $fields->extractLabels();

        try {
            if (is_iterable($value)) {
                foreach ($value as $index => $data) {
                    if ($this instanceof HasRelationship
                        && $this instanceof HasPivot
                        && $fields->isNotEmpty()
                    ) {
                        $pivotAs = $this->getPivotAs($data);

                        $data = tap(
                            $data->{$pivotAs},
                            function ($in) use ($data, $item): void {
                                $in->{$item->{$this->relation()}()->getRelatedPivotKeyName(
                                )} = $data->{$this->resourceTitleField()};
                            }
                        );
                    }

                    if ($this instanceof Json && $this->isKeyOrOnlyValue()) {
                        $data = $this->extractValues([$index => $data]);
                    }

                    if (! $data instanceof Model) {
                        $fields->each(function (FormElement $field) use (&$data): void {
                            if ($field instanceof HasValueExtraction && ! $field instanceof Json) {
                                $data = array_merge(
                                    $data,
                                    $field->extractValues(
                                        $data[$field->field()]
                                    )
                                );
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

        if (! $container) {
            return '';
        }

        return view('moonshine::ui.table', [
            'columns' => $columns,
            'values' => $values,
        ])->render();
    }

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
            && $this->resource()
        ) {
            $this->fields(
                $this->resource()->getFields()
                    ->withoutCanBeRelatable()
                    ->unwrapFields(StackFields::class)
                    ->toArray() ?? []
            );
        }

        $resolveChildFields = $this instanceof HasJsonValues
            || $this instanceof HasPivot
            || ($this instanceof HasResourceMode && ! $this->isResourceMode());

        return Fields::make($this->fields)->when(
            ! $this instanceof Filter && $resolveChildFields,
            fn (Fields $fields): Fields => $fields->resolveChildFields($this)
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
