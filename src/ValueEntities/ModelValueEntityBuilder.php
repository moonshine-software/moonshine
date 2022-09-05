<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ValueEntities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\ValueEntityBuilderContract;

final class ModelValueEntityBuilder implements ValueEntityBuilderContract
{
    public function __construct(protected Model $model)
    {
    }

    public function build(): ModelValueEntity
    {
        return $this->resolveModel($this->model);
    }

    protected function resolveModel(Model $model): ModelValueEntity
    {
        return ModelValueEntity::make(
            $model->getKeyName(),
            $model->getForeignKey(),
            $model->attributesToArray() + $this->resolveRelations($model)
        );
    }

    protected function resolveRelations(Model $model): array
    {
        $attributes = [];

        if (!empty($model->getRelations())) {
            foreach ($model->getRelations() as $key => $value) {
                if ($value instanceof Model) {
                    $attributes[$key] = $this->resolveModel($value);
                } elseif ($value instanceof Collection) {
                    foreach ($value as $k => $v) {
                        $attributes[$key][$k] = $this->resolveModel($v);
                    }
                }
            }
        }

        return $attributes;
    }
}
