<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Traits\Fields\FieldWithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableSelectFieldTrait;

class BelongsTo extends BaseField implements FieldHasRelationContract
{
    use FieldWithRelationshipsTrait, SearchableSelectFieldTrait;

    protected static bool $toOne = true;

    protected static string $view = 'select';

    public function save(Model $item): Model
    {
        return $item->{$this->relation()}()
            ->associate($this->requestValue());
    }
}