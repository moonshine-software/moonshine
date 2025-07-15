<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Core\Core;

/**
 * @template TCore of CoreContract = CoreContract
 */
trait WithCore
{
    /** @var TCore|null */
    private ?CoreContract $core = null;

    /**
     * We don't keep the Core by default, but there is such an option
     * @param TCore $core
     */
    public function setCore(CoreContract $core): void
    {
        $this->core = $core;
    }

    /**
     * @return TCore
     */
    public function getCore(): CoreContract
    {
        if (! \is_null($this->core)) {
            return $this->core;
        }

        /** @var TCore */
        return Core::getInstance();
    }
}
