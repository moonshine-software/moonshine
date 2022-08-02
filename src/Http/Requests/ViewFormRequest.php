<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests;

use Leeto\MoonShine\MoonShineRequest;

class ViewFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('view', $this->findModel());
    }
}
