<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources\Crud;

use MoonShine\UI\Fields\Text;

class TestRefreshingCrudResource extends TestCommentCrudResource
{
    protected function formFields(): iterable
    {
        return [Text::make('Content', 'content')->refreshAfterApply()];
    }
}
