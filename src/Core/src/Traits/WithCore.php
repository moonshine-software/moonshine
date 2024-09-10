<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Core\Core;

/**
 * @template-covariant T of CoreContract
 */
trait WithCore
{
    /**
     * @var ?T $core
     */
    private ?CoreContract $core = null;

    /**
     * We don't keep the Core by default, but there is such an option
     *
     * @param T $core
     */
    public function setCore(CoreContract $core): void
    {
        $this->core = $core;
    }

    /**
     * @return T
     */
    public function getCore(): CoreContract
    {
        if (! is_null($this->core)) {
            return $this->core;
        }

        return Core::getInstance();
    }
}
