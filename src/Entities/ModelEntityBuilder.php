<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\EntityBuilderContract;

final class ModelEntityBuilder implements EntityBuilderContract
{
    public function __construct(protected Model $model)
    {
    }

    public function build(): ModelEntity
    {
        return $this->resolveModel($this->model);
    }

    protected function resolveModel(Model $model): ModelEntity
    {
        return ModelEntity::make(
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
