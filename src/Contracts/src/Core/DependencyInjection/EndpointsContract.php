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
     * @param  string|PageContract|null  $page
     * @param  string|TResource|null  $resource
     * @param  array  $params
     * @param  array  $extra
     *
     * @return mixed
     */
    public function toPage(
        string|PageContract|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        array $extra = [],
    ): mixed;

    /**
     * @param  TResource|null  $resource
     * @param  PageContract|null  $page
     * @param  array  $extra
     *
     * @return string
     */
    public function updateField(
        ?ResourceContract $resource = null,
        ?PageContract $page = null,
        array $extra = []
    ): string;

    /**
     * @param  string  $method
     * @param  string|null  $message
     * @param  array  $params
     * @param  PageContract|null  $page
     * @param  TResource|null  $resource
     *
     * @return string
     */
    public function method(
        string $method,
        ?string $message = null,
        array $params = [],
        ?PageContract $page = null,
        ?ResourceContract $resource = null
    ): string;

    /**
     * @param  string  $name
     * @param  array  $additionally
     *
     * @return string
     */
    public function component(
        string $name,
        array $additionally = []
    ): string;

    /**
     * @param  PageContract|null  $page
     * @param  TResource|null  $resource
     * @param  array  $extra
     *
     * @return string
     */
    public function reactive(
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
        array $extra = []
    ): string;

    /**
     * @return string
     */
    public function home(): string;
}
