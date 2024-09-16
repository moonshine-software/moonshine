<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;

/**
 * @template TResource of ResourceContract
 */
interface EndpointsContract
{
    /**
     * @param  string|TResource|null  $resource
     *
     */
    public function toPage(
        string|PageContract|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        array $extra = [],
    ): mixed;

    /**
     * @param  TResource|null  $resource
     *
     */
    public function updateField(
        ?ResourceContract $resource = null,
        ?PageContract $page = null,
        array $extra = []
    ): string;

    /**
     * @param  TResource|null  $resource
     *
     */
    public function method(
        string $method,
        ?string $message = null,
        array $params = [],
        ?PageContract $page = null,
        ?ResourceContract $resource = null
    ): string;


    public function component(
        string $name,
        array $additionally = []
    ): string;

    /**
     * @param  TResource|null  $resource
     *
     */
    public function reactive(
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
        array $extra = []
    ): string;

    public function home(): string;
}
