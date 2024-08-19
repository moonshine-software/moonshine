<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Models\Comment;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * A duplicate of the TestCommentResource resource was made in order to test the behavior of Switcher->updateOnPreview when the resource is not specified in the method. For this to work in tests, before creating the resource, you must call the fakeRequest method, to which the resourceUri is passed
 */
class TestHasManyCommentResource extends AbstractTestingResource
{
    protected string $model = Comment::class;

    protected int $itemsPerPage = 2;

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Number::make('User id'),
            Text::make('Comment title', 'content')->sortable(),
            //A class has been created for this field
            Switcher::make('Active title', 'active')->updateOnPreview(),
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

    protected function rules(Model $item): array
    {
        return  [
            'content' => 'required',
        ];
    }
}
