<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @method static static make(string $key = 'alert', string $type = 'info', bool $withToast = true, bool $removable = true)
 */
class Flash extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.flash';

    public function __construct(
        protected string $key = 'alert',
        protected string $type = 'info',
        protected bool $withToast = true,
        protected bool $removable = true,
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function viewData(): array
    {
        return [
            'alert' => session()->get($this->key),
            'toast' => $this->withToast ? session()->get('toast') : false,
            'type' => $this->type,
            'withToast' => $this->withToast,
            'removable' => $this->removable,
        ];
    }
}
