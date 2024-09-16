<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Core\Core;

trait WithCore
{
    private ?CoreContract $core = null;

    /**
     * We don't keep the Core by default, but there is such an option
     */
    public function setCore(CoreContract $core): void
    {
        $this->core = $core;
    }

    public function getCore(): CoreContract
    {
        if (! is_null($this->core)) {
            return $this->core;
        }

        return Core::getInstance();
    }
}
