<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests\Resources;

use Leeto\MoonShine\MoonShineRequest;

final class ViewsFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
