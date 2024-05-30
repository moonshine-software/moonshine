<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\CategoryResource;

use MoonShine\Laravel\Pages\Crud\FormPage;

class CategoryPageForm extends FormPage
{
    public function fields(): array
    {
        return [];
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer(),
        ];
    }

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer(),
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
        ];
    }
}
