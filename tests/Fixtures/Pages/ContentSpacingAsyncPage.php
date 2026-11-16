<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages;

use MoonShine\Laravel\Pages\Dashboard;
use MoonShine\Laravel\Traits\Page\WithContentSpacing;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

final class ContentSpacingAsyncPage extends Dashboard
{
    use WithContentSpacing;

    public const NEIGHBOR_MARKER = 'spacing-neighbor-marker';

    protected function components(): iterable
    {
        $items = Item::query()->limit(2)->get();

        return [
            TableBuilder::make()
                ->async()
                ->fields([
                    ID::make(),
                    Text::make('Name title', 'name'),
                ])
                ->items($items)
                ->cast(new ModelCaster(Item::class)),
            FlexibleRender::make(
                '<p data-test="' . self::NEIGHBOR_MARKER . '">Neighbor content</p>'
            ),
        ];
    }
}
