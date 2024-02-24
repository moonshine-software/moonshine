<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use InvalidArgumentException;

trait WithHeadingGradation
{
    protected int $gradation = 3;

    public function getH(): string
    {
        return "h$this->gradation";
    }

    public function h(int $gradation = 3, $asClass = true): static
    {
        if ($gradation < 1 || $gradation > 6) {
            throw new InvalidArgumentException(
                'gradation must be between 1 and 6'
            );
        }

        $this->gradation = $gradation;

        if ($asClass) {
            $this->withAttributes(['class' => $this->getH()]);
        } else {
            $this->tag($this->getH());
        }

        return $this;
    }
}
