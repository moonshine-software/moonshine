<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Builders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ModelValuesBuilder
{
    public function __construct(protected Model $model)
    {
    }

    public function build(): array
    {
        return array_merge(
            $this->resolveModel($this->model),
            $this->resolveRelations($this->model)
        );
    }

    protected function resolveModel(Model $model): array
    {
        $attributes = $model->attributesToArray();
        $attributes['_primaryKeyName'] = $model->getKeyName();

        return $attributes;
    }

    protected function resolveRelations(Model $model): array
    {
        $attributes = [];

        if(!empty($model->getRelations())) {
            foreach ($model->getRelations() as $key => $value) {
                if ($value instanceof Model) {
                    $attributes[$key] = $this->resolveModel($value);
                    # TODO
                    $this->resolveRelations($value);
                } elseif ($value instanceof Collection) {
                    foreach ($value as $k => $v) {
                        $attributes[$key][] = $this->resolveModel($v);
                        # TODO
                        dd($this->resolveRelations($v));
                    }
                }
            }
        }

        return $attributes;
    }
}
