<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasValueExtraction;
use MoonShine\Traits\Fields\SlideTrait;

class SlideField extends Number implements HasValueExtraction, DefaultCanBeArray
{
    use SlideTrait;

    protected static string $view = 'moonshine::fields.slide';

    public function resolvePreview(): string
    {
        $from = $item->{$this->fromField};
        $to = $item->{$this->toField};

        if ($this->withStars()) {
            $from = view('moonshine::ui.rating', [
                'value' => $from,
            ])->render();

            $to = view('moonshine::ui.rating', [
                'value' => $to,
            ])->render();
        }

        return "$from - $to";
    }

    public function exportViewValue(Model $item): string
    {
        return "{$item->{$this->fromField}} - {$item->{$this->toField}}";
    }

    public function formViewValue(Model $item): array
    {
        return $this->extractValues($item->toArray());
    }

    public function extractValues(array $data): array
    {
        return [
            $this->fromField => $data[$this->fromField] ?? $this->min,
            $this->toField => $data[$this->toField] ?? $this->max,
        ];
    }

    public function save(Model $item): Model
    {
        $values = $this->requestValue();

        if ($values === false) {
            return $item;
        }

        $item->{$this->fromField} = $values[$this->fromField] ?? '';
        $item->{$this->toField} = $values[$this->toField] ?? '';

        return $item;
    }
}
