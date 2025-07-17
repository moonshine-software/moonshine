<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Handlers;

use Closure;
use MoonShine\Crud\Handlers\Handler as BaseHandler;

/**
 * @method static static make(Closure|string $label)
 * @deprecated Will be removed in 5.0
 * @see BaseHandler
 */
abstract class Handler extends BaseHandler
{
}
