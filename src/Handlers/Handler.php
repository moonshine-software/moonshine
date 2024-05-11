<?php

declare(strict_types=1);

namespace MoonShine\Handlers;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithQueue;
use MoonShine\Traits\WithUriKey;
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

    public function __construct(Closure|string $label)
    {
        $this->setLabel($label);
    }

    abstract public function handle(): Response;

    abstract public function getButton(): ActionButton;
}
