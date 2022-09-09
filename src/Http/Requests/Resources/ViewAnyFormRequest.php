<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests\Resources;

use Leeto\MoonShine\MoonShineRequest;

final class ViewAnyFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('viewAny', $this->getDataInstance());
    }
}
