<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources\WithCustomPages;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageDetail;
use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageForm;
use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageIndex;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\TinyMce;

class TestCategoryPageResource extends ModelResource
{
    public string $model = Category::class;

    public string $title = 'Category';

    public array $with = ['cover'];

    public string $column = 'name';

    public function pages(): array
    {
        return [
            CategoryPageIndex::make($this->title()),
            CategoryPageForm::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            CategoryPageDetail::make(__('moonshine::ui.show')),
        ];
    }

    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name title', 'name')
                ->sortable(),

            HasOne::make('Cover title', 'cover', resource: new TestCoverPageResource())->fields([
                ID::make(),
                Image::make('HasOne Image title', 'image'),
            ]),
        ];
    }

    public function detailFields(): array
    {
        return $this->indexFields();
    }

    public function formFields(): array
    {
        return [
            Box::make([
                ID::make()->sortable(),

                Text::make('Name title', 'name')
                    ->sortable(),

                TinyMce::make('Content title', 'content'),

                HasOne::make('Cover title', 'cover', resource: new TestCoverPageResource())->fields([
                    ID::make(),
                    Image::make('HasOne Image title', 'image'),
                ]),

                Date::make('Public at title', 'public_at')
                    ->showWhen('is_public', 1)
                    ->withTime(),
            ]),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
