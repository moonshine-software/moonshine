<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\RouterContract;

/**
 * @template-covariant  I of PageContract

 * @mixin I
 */
interface PageContract
{
    public function getUriKey(): string;

    public function getUrl(): string;

    public function getRouter(): RouterContract;

    public function getTitle(): string;
}
