<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Crud\QueryTags\QueryTag;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageIndexWithFeatures;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;

class TestResourceBuilder
{
    public static function new(?string $model = null): TestResource
    {
        $resource = app(TestResource::class);

        if ($model) {
            $resource->setTestModel($model);
        }

        app(CoreContract::class)->resources([$resource]);

        return $resource;
    }

    public static function testResourceWithAllFeatures(): TestResource
    {
        return self::new(
            MoonshineUser::class
        )
            ->setTestFields([
                Text::make('Name'),
                Email::make('Email'),
                Password::make('Password'),
                Preview::make('Badge')->setValue(1)->badge(static fn () => 'red'),
            ])
            ->setTestQueryTags([
                QueryTag::make(
                    'Item #1 Query Tag',
                    static fn ($query) => $query->where('id', 1) // Query builder
                ),
            ])
            ->setTestPages([
                CustomPageIndexWithFeatures::class,
                FormPage::class,
                DetailPage::class,
            ]);
    }
}
