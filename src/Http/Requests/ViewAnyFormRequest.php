<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests;

use Leeto\MoonShine\MoonShineRequest;

class ViewAnyFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('viewAny', $this->getModel());
    }
}
