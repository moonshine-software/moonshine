<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\UI\Fields\Checkbox;
use Throwable;

trait HasHorizontalMode
{
    protected bool $isHorizontal = false;

    protected string $listHtml = '';

    protected string $minColWidth = '200px';

    protected string $maxColWidth = '1fr';

    public function horizontalMode(
        Closure|bool|null $condition = null,
        string $minColWidth = '200px',
        string $maxColWidth = '1fr',
    ): static {
        $this->isHorizontal = value($condition, $this) ?? true;

        if ($this->isHorizontalMode()) {
            $this->minColWidth = $minColWidth;
            $this->maxColWidth = $maxColWidth;
        }

        return $this;
    }

    public function isHorizontalMode(): bool
    {
        return $this->isHorizontal;
    }

    /**
     * @throws Throwable
     */
    public function toListHtml(): string
    {
        $data = $this
            ->resolveValuesQuery()
            ->get();

        $this->listHtml = '';

        return $this->buildList($data);
    }

    /**
     * @throws Throwable
     */
    protected function buildList(Collection $data): string
    {
        foreach ($data as $item) {
            $label = $this->getColumnOrFormattedValue($item, data_get($item, $this->getResourceColumn()));

            $element = Checkbox::make($label)
                ->formName($this->getFormName())
                ->simpleMode()
                ->customAttributes($this->getAttributes()->jsonSerialize())
                ->customAttributes($this->getReactiveAttributes())
                ->setNameAttribute($this->getNameAttribute((string)$item->getKey()))
                ->setValue($item->getKey());

            $this->listHtml .= Str::of((string)$element)->wrap("<li>", "</li>");
        }

        return Str::of($this->listHtml)->wrap(
            "<ul class='horizontal-list' style='grid-template-columns: repeat(auto-fill, minmax($this->minColWidth, $this->maxColWidth))'>",
            "</ul>",
        )->value();
    }
}
