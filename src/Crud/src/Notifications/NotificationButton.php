<?php

declare(strict_types=1);

namespace MoonShine\Crud\Notifications;

use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Crud\Contracts\Notifications\NotificationButtonContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class NotificationButton implements NotificationButtonContract, Arrayable
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private string $label,
        private string $link,
        private array $attributes = [],
    ) {

    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getAttributes(): ComponentAttributesBagContract
    {
        return new MoonShineComponentAttributeBag($this->attributes);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'link' => $this->getLink(),
            'attributes' => $this->getAttributes()->getAttributes(),
        ];
    }
}
