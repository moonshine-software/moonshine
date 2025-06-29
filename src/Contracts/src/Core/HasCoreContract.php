<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;

/**
 * @template T of CoreContract = CoreContract
 */
interface HasCoreContract
{
    /**
     * @param  T  $core
     */
    public function setCore(CoreContract $core): void;

    /**
     * @return T
     */
    public function getCore(): CoreContract;
}
