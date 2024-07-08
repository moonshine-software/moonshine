<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\Support\Enums\FlashType;
use MoonShine\UI\Components\MoonShineComponent;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @method static static make(string $key = 'alert', string|FlashType $type = FlashType::INFO, bool $withToast = true, bool $removable = true)
 */
class Flash extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.flash';

    public function __construct(
        protected string $key = 'alert',
        protected string|FlashType $type = FlashType::INFO,
        protected bool $withToast = true,
        protected bool $removable = true,
    ) {
        parent::__construct();

        $this->type = $this->type instanceof FlashType ? $this->type->value : $this->type;
    }

    /**
     * @return array<string, mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function viewData(): array
    {
        return [
            'alert' => moonshine()->getRequest()->getSession($this->key),
            'toast' => $this->withToast ? moonshine()->getRequest()->getSession('toast') : false,
            'type' => $this->type,
            'withToast' => $this->withToast,
            'removable' => $this->removable,
        ];
    }
}
