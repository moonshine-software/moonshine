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
use MoonShine\UI\Contracts\Actions\ActionButtonContract;
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

    protected ?Closure $modifyButton = null;

    public function __construct(Closure|string $label)
    {
        $this->setLabel($label);
    }

    abstract public function handle(): Response;

    abstract public function getButton(): ActionButtonContract;

    public function getUrl(): string
    {
        return $this->getResource()?->getRoute('handler', query: ['handlerUri' => $this->getUriKey()]) ?? '';
    }

    /**
     * @param  Closure(ActionButtonContract $button, static $ctx): ActionButtonContract  $closure
     */
    public function modifyButton(Closure $closure): static
    {
        $this->modifyButton = $closure;

        return $this;
    }

    protected function prepareButton(ActionButtonContract $button): ActionButtonContract
    {
        if(! is_null($this->modifyButton)) {
            return value($this->modifyButton, $button, $this);
        }

        return $button;
    }
}
