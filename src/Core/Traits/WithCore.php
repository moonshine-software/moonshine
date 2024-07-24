<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Core\Core;

trait WithCore
{
    private ?CoreContract $core = null;

    // todo(hot)-1 ???
    private static ?CoreContract $coreInstance = null;

    public function setCore(CoreContract $core): void
    {
        $this->core = $core;
    }

    // todo(hot)-1 ???
    public static function setCoreInstance(CoreContract $core): void
    {
        static::$coreInstance = $core;
    }

    public function getCore(): CoreContract
    {
        if(! is_null($this->core)) {
            return $this->core;
        }

        if(! is_null(static::$coreInstance)) {
            return static::$coreInstance;
        }

        return $this->core = Core::getInstance();
    }
}
