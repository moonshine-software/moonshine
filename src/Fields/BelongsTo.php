<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Traits\Fields\WithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableTrait;

class BelongsTo extends Field implements HasRelationshipContract
{
    use WithRelationshipsTrait, SearchableTrait;

    protected static bool $toOne = true;

    protected static string $view = 'select';

    public function save(Model $item): Model
    {
        return $item->{$this->relation()}()
            ->associate($this->requestValue());
    }
}
