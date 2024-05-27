<?php

declare(strict_types=1);

namespace MoonShine\Core\Handlers;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Support\Traits\HasResource;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithIcon;
use MoonShine\Support\Traits\WithLabel;
use MoonShine\Support\Traits\WithQueue;
use MoonShine\Support\Traits\WithUriKey;
use MoonShine\UI\Components\ActionButton;
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
