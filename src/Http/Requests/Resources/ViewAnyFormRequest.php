<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests\Resources;

use Leeto\MoonShine\MoonShineRequest;

final class ViewAnyFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('viewAny', $this->getResource()->getModel());
    }

    public function hasQueryTag(): bool
    {
        return !is_null($this->route('queryTag'));
    }

    public function getQueryTag(): ?string
    {
        return $this->route('queryTag');
    }
}
