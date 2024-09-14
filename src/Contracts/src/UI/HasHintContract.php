<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

interface HasHintContract
{
    public function hint(string $hint): static;

    public function getHint(): string;
}
