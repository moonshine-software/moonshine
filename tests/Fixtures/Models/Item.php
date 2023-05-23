<?php

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MoonShine\Tests\Fixtures\Models\Traits\MorphRelationTrait;
use MoonShine\Tests\Fixtures\Models\Traits\UserHasOneTrait;

class Item extends Model
{
    use UserHasOneTrait;

    use MorphRelationTrait;

    protected $fillable = [
        'name',
        'content',
        'category_id',
        'moonshine_user_id',
        'public_at',
        'created_at',
        'updated_at'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
