<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Casts\ModelCast;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\Makeable;

final class DetailCardBuilder extends RowComponent
{
    use Makeable;

    public string $title = '';

    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function fillFromModelResource(ModelResource $resource): self
    {
        return $this
            ->fields($resource->getFields()->toArray())
            ->fill($resource->getItem()?->attributesToArray() ?? [])
            ->cast(ModelCast::make($resource->getModel()::class));
    }

    public function render(): View|Closure|string
    {
        return view('moonshine::components.detail-card.builder', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'title' => $this->title,
            'fields' => $this->getFields()->onlyFields(),
            'buttons' => $this->getButtons()
        ]);
    }
}