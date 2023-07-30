<?php

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Decorations\Block;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\Date;
use MoonShine\Fields\Email;
use MoonShine\Fields\File;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Relationships\MorphMany;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Models\Category;

class TestResourceBuilder
{
    public static function new(string $model = null, bool $addRoutes = false): TestResource
    {
        $resource = new TestResource();

        if ($model) {
            $resource->setTestModel($model);
        }

        if ($addRoutes) {
            $resource->addRoutes();
        }

        return $resource;
    }

    public static function buildForCanSeeTest(): TestResource
    {
        return self::new()->setTestFields([
            ID::make()
                ->sortable()
                ->showOnExport(),

            Text::make('Name', 'name')
                ->canSee(fn ($item): bool => $item->id === 2)
                ->showOnExport(),

            Email::make('Email', 'email')
                ->sortable()
                ->showOnExport()
                ->required(),
        ]);
    }

    public static function buildWithFields(): TestResource
    {
        return self::new()->setTestFields([
            ID::make(),
            Text::make('Name', 'name'),
        ]);
    }

    public static function buildCategoryResource(): TestResource
    {
        return self::new()
            ->setTestModel(Category::class)
            ->setTestFields(
                [
                    Block::make('', [
                        Tabs::make([
                            Tab::make('Редактирование', [
                                ID::make()->sortable(),

                                Text::make('Имя', 'name')->sortable(),

                                Text::make('Контент', 'content')->hideOnIndex(),

                                HasOne::make('Обложка', 'image')->fields([
                                    ID::make()->sortable(),
                                    Image::make('Файл', 'name')
                                        ->dir('category_images')
                                        ->removable(),
                                ])->removable(),

                                Date::make('Дата публикации', 'public_at')
                                    ->hideOnIndex()
                                    ->showWhen('is_public', 1)
                                    ->withTime(),
                            ]),

                            Tab::make('Изображения', [
                                MorphMany::make('', 'images')
                                    ->fields([
                                        ID::make()->sortable(),
                                        Image::make('Файл', 'name')
                                            ->dir('category_gallery')
                                            ->removable()
                                            ->enableDeleteDir(),
                                    ])
                                    ->hideOnIndex()
                                    ->removable(),
                            ]),

                            Tab::make('Файлы', [
                                MorphMany::make('', 'files')
                                    ->fields([
                                        ID::make()->sortable(),
                                        File::make('Файл', 'name')
                                            ->dir('category_files')
                                            ->multiple()
                                            ->removable(),
                                    ])
                                    ->hideOnIndex()
                                    ->removable(),
                            ]),
                        ]),
                    ]),
                ]
            );
    }
}
