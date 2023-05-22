<?php

namespace MoonShine\Tests\Fixtures\Models\Traits;


use Illuminate\Database\Eloquent\Relations\MorphMany;
use MoonShine\Tests\Fixtures\Models\FileModel;
use MoonShine\Tests\Fixtures\Models\ImageModel;

trait MorphRelationTrait
{
    public function images(): MorphMany
    {
        return $this->morphMany(ImageModel::class, 'imageable')->orderBy('sort_number');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(FileModel::class, 'fileable')->orderBy('sort_number');
    }
}
