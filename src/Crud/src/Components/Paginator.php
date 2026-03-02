<?php

declare(strict_types=1);

namespace MoonShine\Crud\Components;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Crud\TypeCasts\PaginatorCaster;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(PaginatorContract|CursorPaginator $paginator)
 */
final class Paginator extends MoonShineComponent
{
    protected string $view = 'moonshine::components.pagination';

    /**
     * @return array<string, mixed>
     */
    public function getTranslates(): array
    {
        /**
         * @var array<string, mixed>
         */
        return $this->getCore()->getTranslator()->get('moonshine::pagination');
    }

    /**
     * @param PaginatorContract<array-key, mixed>|CursorPaginator<array-key, mixed> $paginator
     */
    public function __construct(
        private readonly PaginatorContract|CursorPaginator $paginator
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $escapeUi = (bool) $this->getCore()->getConfig()->get('html_escaping.ui_elements', false);

        /**
         * @var (PaginatorContract<array-key, mixed>|CursorPaginator<array-key, mixed>)&Arrayable<array-key, mixed> $data
         */
        $data = $this->paginator;

        $pageName = method_exists($data, 'getPageName') ? $data->getPageName() : 'page';

        /**
         * @var (PaginatorContract<array-key, mixed>|CursorPaginator<array-key, mixed>)&Arrayable<array-key, mixed> $appended
         */
        $appended = $data->appends(
            $this->getCore()->getRequest()->getExcept($pageName)
        );

        $paginator = (new PaginatorCaster(
            $appended->toArray(),
            $data->items()
        ))->cast();

        /**
         * @var array<string, mixed>
         */
        return [
            ...$paginator->toArray(),
            'escapeUi' => $escapeUi,
        ];
    }
}
