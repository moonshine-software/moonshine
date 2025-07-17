<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

/**
 * @internal
 * @template TData
 * @template-covariant TIndexPage of null|CrudPageContract = null
 * @template-covariant TFormPage of null|CrudPageContract = null
 * @template-covariant TDetailPage of null|CrudPageContract = null
 *
 */
interface CrudResourceWithPagesContract
{
    public function setActivePage(?PageContract $page): void;

    /**
     * @return null|TIndexPage
     */
    public function getIndexPage(): ?PageContract;

    /**
     * @return null|TFormPage
     */
    public function getFormPage(): ?PageContract;

    /**
     * @return null|TDetailPage
     */
    public function getDetailPage(): ?PageContract;

    public function getActivePage(): ?PageContract;

    /**
     * @param  string|PageContract  $page
     * @param  array<string, mixed>  $params
     * @param  string|string[]|null  $fragment
     *
     * @return string
     */
    public function getPageUrl(string|PageContract $page, array $params = [], null|string|array $fragment = null): string;

    /**
     * @param  array<string, mixed>  $params
     * @param  string|string[]|null  $fragment
     *
     * @return string
     */
    public function getIndexPageUrl(array $params = [], null|string|array $fragment = null): string;

    /**
     * @param DataWrapperContract<TData>|int|string|null $key
     * @param  array<string, mixed>  $params
     * @param  string|string[]|null  $fragment
     */
    public function getFormPageUrl(
        DataWrapperContract|int|string|null $key = null,
        array $params = [],
        null|string|array $fragment = null
    ): string;

    /**
     * @param DataWrapperContract<TData>|int|string $key
     * @param  array<string, mixed>  $params
     * @param  string|string[]|null  $fragment
     */
    public function getDetailPageUrl(
        DataWrapperContract|int|string $key,
        array $params = [],
        null|string|array $fragment = null
    ): string;

    /**
     * @param  string|string[]  $fragment
     * @param  array<string, string|int|float|null>  $params
     */
    public function getFragmentLoadUrl(
        string|array $fragment,
        ?PageContract $page = null,
        DataWrapperContract|int|string|null $key = null,
        array $params = []
    ): string;

    /**
     * @param  array<string, string|int|float|null>  $params
     */
    public function getAsyncMethodUrl(
        string $method,
        ?string $message = null,
        array $params = [],
        ?PageContract $page = null,
    ): string;

    public function isIndexPage(): bool;

    public function isFormPage(): bool;

    public function isDetailPage(): bool;

    public function isCreateFormPage(): bool;

    public function isUpdateFormPage(): bool;
}
