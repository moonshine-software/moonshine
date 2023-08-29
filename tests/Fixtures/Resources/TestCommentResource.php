<?php

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Comment;

class TestCommentResource extends ModelResource
{
    protected string $model = Comment::class;

    public function fields(): array
    {
        return [
            ID::make(),
            Text::make('Comment title', 'content')
        ];
    }

    public function rules(Model $item): array
    {
        return  [
            'content' => 'required'
        ];
    }
}