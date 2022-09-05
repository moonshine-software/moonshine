<?php

declare(strict_types=1);

namespace Leeto\MoonShine\RowActions;

use JsonSerializable;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;
use Leeto\MoonShine\Traits\WithIcon;

abstract class RowAction implements JsonSerializable
{
    use Makeable;
    use WithIcon;
    use WithComponentAttributes;

    protected bool $withConfirm = false;

    public function __construct(
        protected string $title,
    ) {
    }

    # TODO
    abstract public function route(array $params = []): string;

    public function title(): string
    {
        return $this->title;
    }

    # TODO
    public function confirm(
        string $title,
        string $successButton,
        string $cancelButton,
        string $icon = null
    ): static {
        $this->withConfirm = true;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title(),
            'icon' => $this->getIcon(),
            'route' => $this->route(),
            'attributes' => $this->attributes()->getAttributes(),
            # TODO
            'confirm' => [
                ''
            ]
        ];
    }
}
