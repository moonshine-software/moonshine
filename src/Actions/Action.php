<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use JsonSerializable;
use Leeto\MoonShine\Http\Requests\Resources\ActionFormRequest;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithUriKey;

abstract class Action implements JsonSerializable
{
    use Makeable;
    use WithUriKey;

    final public function __construct(protected string $label)
    {
    }

    abstract public function handle(ActionFormRequest $request): mixed;

    public function url(): string
    {
        return route(
            config('moonshine.prefix').'.action', [$this->uriKey(), ...request()->query()]
        );
    }

    public function label(): string
    {
        return $this->label;
    }

    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label(),
            'url' => $this->url(),
        ];
    }
}
