<?php

declare(strict_types=1);

namespace MoonShine;

use MoonShine\Applies\Filters\BelongsToManyModelApply;
use MoonShine\Applies\Filters\CheckboxModelApply;
use MoonShine\Applies\Filters\DateModelApply;
use MoonShine\Applies\Filters\DateRangeModelApply;
use MoonShine\Applies\Filters\JsonModelApply;
use MoonShine\Applies\Filters\MorphToModelApply;
use MoonShine\Applies\Filters\RangeModelApply;
use MoonShine\Applies\Filters\TextModelApply;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Date;
use MoonShine\Fields\DateRange;
use MoonShine\Fields\Json;
use MoonShine\Fields\Range;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Resources\ModelResource;

final class MoonShineRegister
{
    private string $activeOption = '';

    private string $activeSection = '';

    private array $options = [
        'filters' => [
            ModelResource::class => [
                Date::class => DateModelApply::class,
                Range::class => RangeModelApply::class,
                    DateRange::class => DateRangeModelApply::class,
                    BelongsToMany::class => BelongsToManyModelApply::class,
                    MorphTo::class => MorphToModelApply::class,
                    Json::class => JsonModelApply::class,
                    Text::class => TextModelApply::class,
                    Textarea::class => TextModelApply::class,
                    Checkbox::class => CheckboxModelApply::class,
                ],
            ],
            'fields' => [],
        ];

    public function activeOption(string $activeOption): MoonShineRegister
    {
        $this->activeOption = $activeOption;

        return $this;
    }

    public function for(string $activeSection): MoonShineRegister
    {
        $this->activeSection = $activeSection;

        return $this;
    }

    public function filters(): MoonShineRegister
    {
        $this->activeOption('filters');

        return $this;
    }

    public function fields(): MoonShineRegister
    {
        $this->activeOption('fields');

        return $this;
    }

    public function set(string $key, string $value): MoonShineRegister
    {
        if(! $this->issetOption()) {
            $this->options[$this->activeOption][$this->activeSection] = [];
        }

        if(! empty($this->options[$this->activeOption][$this->activeSection][$key])) {
            return $this;
        }

        $this->options[$this->activeOption][$this->activeSection][$key] = $value;

        return $this;
    }

    public function get(string $key): mixed
    {
        if(! $this->issetOption()) {
            return null;
        }

        if(
            (! $result = $this->options[$this->activeOption][$this->activeSection][$key] ?? null)
            && class_exists($key)
        ) {
            foreach ($this->options[$this->activeOption][$this->activeSection] as $fieldApply => $applyClasse) {
                if(is_subclass_of($key, $fieldApply)) {
                    $result = $applyClasse;

                    break;
                }
            }
        }

        return $result ?? null;
    }

    private function issetOption(): bool
    {
        return isset($this->options[$this->activeOption][$this->activeSection]);
    }
}
