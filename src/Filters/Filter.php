<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasAssets;
use Leeto\MoonShine\Contracts\Fields\HasFormViewValue;
use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Traits\Fields\FormElement;
use Leeto\MoonShine\Traits\Fields\HasCanSee;
use Leeto\MoonShine\Traits\Fields\ShowWhen;
use Leeto\MoonShine\Traits\Fields\WithHtmlAttributes;
use Leeto\MoonShine\Traits\Fields\XModel;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithAssets;
use Leeto\MoonShine\Traits\WithView;
use Leeto\MoonShine\Utilities\AssetManager;

abstract class Filter implements HtmlViewable, HasAssets, HasFormViewValue
{
    use FormElement;
    use Makeable;
    use ShowWhen;
    use WithAssets;
    use WithHtmlAttributes;
    use WithView;
    use XModel;
    use HasCanSee;

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            app(AssetManager::class)->add($this->getAssets());
        }
    }

    public function name(string $index = null): string
    {
        return $this->prepareName($index, 'filters');
    }

    public function getQuery(Builder $query): Builder
    {
        if ($this->hasRelationship()) {
            $related = $this->getRelated($query->getModel());

            return $this->requestValue()
                ? $query->whereHas($this->relation(), function (Builder $q) use ($related) {
                    $table = $q->getModel()->getTable();
                    $id = $related->getKeyName();

                    return $q->whereIn(
                        "$table.$id",
                        is_array($this->requestValue())
                            ? $this->requestValue()
                            : [$this->requestValue()]
                    );
                })
                : $query;
        }

        return $this->requestValue() !== false
            ? $query->where($this->field(), $this->requestValue())
            : $query;
    }

    public function formViewValue(Model $item): mixed
    {
        return $this->requestValue();
    }
}
