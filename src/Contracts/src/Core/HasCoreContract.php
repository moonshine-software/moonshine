<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;

/**
 * @template-covariant T of CoreContract
 */
interface HasCoreContract
{
    public function setCore(CoreContract $core): void;

    /**
     * @return T
     */
    public function getCore(): CoreContract;
}
