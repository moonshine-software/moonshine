<?php

declare(strict_types=1);

namespace MoonShine\Laravel\TypeCasts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\MoonShineDataCaster;
use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Core\Paginator\PaginatorContract;

/**
 * @template-covariant T of Model
 */
final readonly class ModelCaster implements MoonShineDataCaster
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
            $data->exists = ! empty($data->getKey());
        }

        return new ModelCastedData($data);
    }

    /**
     * @throws MoonShineException
     */
    public function paginatorCast(mixed $data): ?PaginatorContract
    {
        if(! $data instanceof Paginator) {
            return null;
        }

        $paginator = new PaginatorCaster(
            $data->appends(
                moonshine()->getRequest()->getExcept('page')
            )->toArray()
        );

        return $paginator->cast();
    }
}
