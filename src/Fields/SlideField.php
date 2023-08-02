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

    protected function resolvePreview(): string
    {
        $item = null;

        if ($this->isRawMode()) {
            return "{$item->{$this->fromField}} - {$item->{$this->toField}}";
        }

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

    protected function resolveValue(): array
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
