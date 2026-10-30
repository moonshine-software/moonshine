<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Pages\Crud\FormPage;

class TestValidationFormPage extends FormPage
{
    protected function rules(DataWrapperContract $item): array
    {
        return ['name' => ['required', 'string', 'max:10']];
    }
}
