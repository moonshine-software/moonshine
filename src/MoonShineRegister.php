<?php

declare(strict_types=1);

namespace MoonShine;

use MoonShine\Applies\Filters\BelongsToManyModelApply;
use MoonShine\Applies\Filters\DateModelApply;
use MoonShine\Applies\Filters\JsonModelApply;
use MoonShine\Applies\Filters\RangeModelApply;
use MoonShine\Applies\Filters\SlideModelApply;
use MoonShine\Fields\Date;
use MoonShine\Fields\Json;
use MoonShine\Fields\RangeField;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\SlideField;
use MoonShine\Resources\ModelResource;

final class MoonShineRegister
{
    private string $activeOption = '';

    private string $activeSection = '';

    private array $options = [
        'filters' => [
            ModelResource::class => [
                Date::class => DateModelApply::class,
                RangeField::class => RangeModelApply::class,
                SlideField::class => SlideModelApply::class,
                BelongsToMany::class => BelongsToManyModelApply::class,
                Json::class => JsonModelApply::class,
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
        if($this->issetOption()) {
            return $this;
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

        return $this->options[$this->activeOption][$this->activeSection][$key] ?? null;
    }

    private function issetOption(): bool
    {
        return isset($this->options[$this->activeOption][$this->activeSection]);
    }
}
