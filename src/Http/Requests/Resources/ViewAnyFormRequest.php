<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonShineFormRequest;

final class ViewAnyFormRequest extends MoonShineFormRequest
{
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        return $this->getResource()->can('viewAny');
    }

    public function hasQueryTag(): bool
    {
        return ! is_null($this->route('queryTag'));
    }

    public function getQueryTag(): ?string
    {
        return $this->route('queryTag');
    }
}
