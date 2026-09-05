<?php

declare(strict_types=1);

namespace MoonShine\Laravel\TypeCasts;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Crud\TypeCasts\PaginatorCaster;

/**
 * @template  T of Model
 *
 * @implements DataCasterContract<T>
 */
final readonly class ModelCaster implements DataCasterContract
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
     * @return ModelDataWrapper<T>
     */
    public function cast(mixed $data): ModelDataWrapper
    {
        if (\is_array($data)) {
            $model = new ($this->getClass());
            $data = $model->forceFill($data);
            $data->exists = ! empty($data->getKey());
        }

        /** @noRector */
        return new ModelDataWrapper($data);
    }

    public function paginatorCast(mixed $data): ?PaginatorContract
    {
        if (! $data instanceof Paginator && ! $data instanceof CursorPaginator) {
            return null;
        }

        /** @var string $pageName */
        $pageName = method_exists($data, 'getPageName') ? $data->getPageName() : 'page';

        /**
         * @var (Paginator<array-key, T>|CursorPaginator<array-key, T>)&Arrayable<array-key, mixed> $data
         */
        $paginator = new PaginatorCaster(
            $data->appends(
                moonshine()->getRequest()->getExcept($pageName)
            )->toArray(),
            $data->items(),
            pageName: $pageName
        );

        return $paginator->cast();
    }
}
