<?php

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MoonShine\Tests\Fixtures\Factories\ItemFactory;
use MoonShine\Tests\Fixtures\Models\Traits\MorphRelationTrait;
use MoonShine\Tests\Fixtures\Models\Traits\UserHasOneTrait;

class Item extends Model
{
    use UserHasOneTrait;

    use MorphRelationTrait;

    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'category_id',
        'moonshine_user_id',
        'public_at',
        'created_at',
        'updated_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'item_id');
    }

    protected static function newFactory()
    {
        return new ItemFactory();
    }
}
