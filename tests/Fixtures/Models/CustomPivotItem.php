<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomPivotItem extends Item
{
    protected $table = 'items';

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id')
            ->as('membership')
            ->withPivot(['pivot_1', 'pivot_2', 'pivot_3']);
    }

    public function keyedCategories(): BelongsToMany
    {
        return $this->belongsToMany(CustomKeyCategory::class, 'category_item', 'item_id', 'category_id', 'id', 'category_id');
    }
}
