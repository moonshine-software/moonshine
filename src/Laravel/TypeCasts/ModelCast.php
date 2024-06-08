<?php

declare(strict_types=1);

namespace MoonShine\Laravel\TypeCasts;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\MoonShineDataCast;

/**
 * @template-covariant T of Model
 */
final readonly class ModelCast implements MoonShineDataCast
{
    public function __construct(
        /** @var class-string<T> $class */
        private string $class
    ) {
    }

    /** @return class-string<T> $class */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return CastedData<T>
     */
    public function cast(mixed $data): CastedData
    {
        if(is_array($data)) {
            /** @var T $model */
            $model = new ($this->getClass());
            $data = $model->forceFill($data);
        }

        return new ModelCastedData($data);
    }
}
