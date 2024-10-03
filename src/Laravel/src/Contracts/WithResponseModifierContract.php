<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface WithResponseModifierContract
{
    public function isResponseModified(): bool;

    public function getModifiedResponse(): ?Response;
}
