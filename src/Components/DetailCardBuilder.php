<?php

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use MoonShine\Casts\ModelCast;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\ComponentButtons;
use MoonShine\Traits\Fields\FieldValues;
use MoonShine\Traits\HasDataCast;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\StringRendeable;

final class DetailCardBuilder extends Component implements MoonShineRenderable
{
    use Makeable;
    use FieldValues;
    use StringRendeable;
    use ComponentButtons;
    use HasDataCast;

    public string $title;

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

    public function getCastedData(): mixed
    {
        return $this->hasCast()
            ? $this->getCast()->hydrate($this->getValues())
            : $this->getValues();
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