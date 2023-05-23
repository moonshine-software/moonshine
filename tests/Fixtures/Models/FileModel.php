<?php

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class FileModel extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'name' => 'array'
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
