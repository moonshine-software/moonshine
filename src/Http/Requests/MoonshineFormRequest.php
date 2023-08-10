<?php

namespace MoonShine\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MoonshineFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function getResource(): ModelResource
    {
        return moonshineRequest()->getResource();
    }

    public function getPage(): Page
    {
        return moonshineRequest()->getPage();
    }

    public function redirectRoute(string $default): RedirectResponse
    {
        return redirect($default);
    }
}
