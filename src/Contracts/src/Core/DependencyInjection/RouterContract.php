<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\StatefulContract;

/**
 * @template-covariant I of RouterContract = RouterContract
 * @template-covariant E of EndpointsContract = EndpointsContract

 * @mixin I
 */
interface RouterContract extends StatefulContract
{
    /**
     * @param  array<string, mixed>  $params
     */
    public function to(string $name = '', array $params = []): string;

    /**
     * @return E
     */
    public function getEndpoints(): EndpointsContract;

    public function extractPageUri(?PageContract $page = null): ?string;

    public function extractResourceUri(?ResourceContract $resource = null): ?string;

    public function extractResourceItem(
        int|string|null $key = null,
        ?CrudResourceContract $resource = null
    ): string|int|null;

    public function withPage(?PageContract $page = null): static;

    public function withResource(?ResourceContract $resource = null): static;

    public function withResourceItem(int|string|null $key = null, ?CrudResourceContract $resource = null): static;

    public function getParam(string $key, mixed $default = null): mixed;

    public function forgetParam(string $key): static;

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function getParams(array $params = []): array;

    /**
     * @param  array<string, mixed>  $params
     */
    public function withParams(array $params): static;
}
