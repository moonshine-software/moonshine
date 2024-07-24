<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Core\Core;

trait WithCore
{
    private ?CoreContract $core = null;

    public function setCore(CoreContract $core): void
    {
        $this->core = $core;
    }

    public function getCore(): CoreContract
    {
        if(! is_null($this->core)) {
            return $this->core;
        }

        return $this->core = Core::getInstance();
    }
}
