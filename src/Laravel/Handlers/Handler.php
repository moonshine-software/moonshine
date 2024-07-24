<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Handlers;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Core\Traits\HasResource;
use MoonShine\Core\Traits\WithCore;
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
    use WithCore;
    use Conditionable;

    protected ?Closure $modifyButton = null;

    protected array|Closure $notifyUsers = [];

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

    /**
     * @param array|Closure(static $ctx): array $ids
     */
    public function notifyUsers(array|Closure $ids): static
    {
        $this->notifyUsers = $ids;

        return $this;
    }

    public function getNotifyUsers(): array
    {
        return value($this->notifyUsers, $this);
    }
}
