<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Handlers;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Core\Traits\HasResource;
use MoonShine\Core\Traits\WithUriKey;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithQueue;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method static static make(Closure|string $label)
 */
abstract class Handler
{
    use Makeable;
    use WithQueue;
    use HasResource;
    use WithIcon;
    use WithUriKey;
    use WithLabel;
    use Conditionable;

    protected CoreContract $core;

    public function __construct(Closure|string $label)
    {
        $this->setLabel($label);

        // todo DI
        $this->core = app(CoreContract::class);
    }

    abstract public function handle(): Response;

    abstract public function getButton(): ActionButtonContract;
}
