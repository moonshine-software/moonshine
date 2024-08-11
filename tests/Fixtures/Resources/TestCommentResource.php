<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Models\Comment;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

class TestCommentResource extends AbstractTestingResource
{
    protected string $model = Comment::class;

    protected int $itemsPerPage = 2;

    protected function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            Number::make('User id'),
            Text::make('Comment title', 'content')->sortable(),
        ];
    }

    protected function formFields(): array
    {
        return $this->indexFields();
    }

    protected function detailFields(): array
    {
        return $this->indexFields();
    }

    protected function rules(Model $item): array
    {
        return  [
            'content' => 'required',
        ];
    }
}
