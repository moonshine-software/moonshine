<?php

namespace Leeto\MoonShine\Contracts\Fields;

interface HasRelationshipContract
{
    public function isRelationToOne(): bool;

    public function isRelationHasOne(): bool;
}