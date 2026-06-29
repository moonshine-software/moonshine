<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MoonShine\Tests\Fixtures\Factories\CommentFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'item_id',
        'data',
        'imageable_id',
        'imageable_type',
    ];

    protected $casts = [
        'data' => 'collection',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory()
    {
        return new CommentFactory();
    }
}
