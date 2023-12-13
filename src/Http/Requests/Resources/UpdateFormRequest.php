<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonShineFormRequest;

final class UpdateFormRequest extends MoonShineFormRequest
{
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        if (! in_array(
            'update',
            $this->getResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->getResource()->can('update');
    }

    public function rules(): array
    {
        return $this->getResource()->rules(
            $this->getResource()->getItemOrFail()
        );
    }
}
