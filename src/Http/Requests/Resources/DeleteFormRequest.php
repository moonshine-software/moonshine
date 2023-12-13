<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonShineFormRequest;

final class DeleteFormRequest extends MoonShineFormRequest
{
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

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
