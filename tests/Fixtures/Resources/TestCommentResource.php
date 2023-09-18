<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\ID;
use MoonShine\Fields\Number;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Comment;

class TestCommentResource extends ModelResource
{
    protected string $model = Comment::class;

    protected int $itemsPerPage = 2;

    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Number::make('User id'),
            Text::make('Comment title', 'content')->sortable(),
        ];
    }

    public function rules(Model $item): array
    {
        return  [
            'content' => 'required',
        ];
    }
}
