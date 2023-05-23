<?php

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ImageModel extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'name',
        'created_at',
        'updated_at',
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
