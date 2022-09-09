<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ViewComponents;

use JsonSerializable;
use Leeto\MoonShine\Contracts\HasEndpoint;
use Leeto\MoonShine\Contracts\ViewComponentContract;
use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponent;
use Leeto\MoonShine\Traits\WithComponentAttributes;
use Leeto\MoonShine\Traits\WithUriKey;

class MoonShineViewComponent implements ViewComponentContract, HasEndpoint, JsonSerializable
{
    use Makeable;
    use WithComponentAttributes;
    use WithUriKey;
    use WithComponent;

    protected array $endpointData = [];

    protected string $endpointName = 'view-component';

    public function jsonSerialize(): array
    {
        return [
            'component' => $this->getComponent(),
            'attributes' => $this->attributes()->getAttributes(),
            'endpoint' => $this->endpoint()
        ];
    }

    public function endpointName(string $name): static
    {
        $this->endpointName = $name;

        return $this;
    }

    public function endpointData(array $data): static
    {
        $this->endpointData = $data;

        return $this;
    }

    public function endpoint(): string
    {
        return MoonShineRouter::to($this->endpointName, [
            'componentUri' => $this->uriKey(),

            ...$this->endpointData
        ]);
    }
}
