<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Resources;

use Leeto\MoonShine\Contracts\EntityContract;

interface HasEntity
{
    public function setEntityId(string|int $entityId): self;

    public function getEntityId(): string|int|null;

    public function entity(mixed $values): EntityContract;
}
