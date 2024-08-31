<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Tests\Fixtures\Models\Comment;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

class TestCommentResource extends AbstractTestingResource
{
    protected string $model = Comment::class;

    protected int $itemsPerPage = 2;

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Number::make('User id'),
            Text::make('Comment title', 'content')->sortable(),
        ];
    }

    protected function formFields(): iterable
    {
        return $this->indexFields();
    }

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    protected function rules(mixed $item): array
    {
        return  [
            'content' => 'required',
        ];
    }
}
