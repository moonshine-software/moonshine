<?php

namespace Leeto\MoonShine\Contracts\Fields;

interface FieldHasRelationContract
{
    public function isRelationToOne(): bool;

    public function isRelationHasOne(): bool;
}