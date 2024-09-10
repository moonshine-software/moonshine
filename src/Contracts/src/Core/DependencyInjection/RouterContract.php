<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;

/**
 * @template-covariant I of RouterContract

 * @mixin I
 */
interface RouterContract
{
    public function to(string $name = '', array $params = []): string;

    public function getEndpoints(): EndpointsContract;

    public function extractPageUri(?PageContract $page = null): ?string;

    public function extractResourceUri(?ResourceContract $resource = null): ?string;
}
