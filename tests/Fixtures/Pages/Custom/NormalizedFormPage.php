<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Laravel\Pages\Crud\FormPage;

class NormalizedFormPage extends FormPage
{
    public function prepareForValidation(): void
    {
        $name = $this->getCore()->getRequest()->getScalar('name', '');
        request()->merge(['name' => strtoupper((string) $name)]);
    }
}
