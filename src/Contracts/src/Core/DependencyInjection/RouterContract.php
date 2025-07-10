<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

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

    public function withPage(?PageContract $page = null): static;

    public function withResource(?ResourceContract $resource = null): static;
}
