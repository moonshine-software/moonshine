<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\MoonShineRequest;

final class DeleteFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        if (! in_array(
            'delete',
            $this->getResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->getResource()->can('delete');
    }
}
