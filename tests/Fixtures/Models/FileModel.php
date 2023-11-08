<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileModel extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'path',
        'item_id',
        'created_at',
        'updated_at',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
