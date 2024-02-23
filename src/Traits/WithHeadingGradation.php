<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithHeadingGradation
{
    protected int $gradation = 3;

    public function getH(): string
    {
        return "h$this->gradation";
    }

    public function h(int $gradation = 3, $asClass = true): static
    {
        $this->gradation = $gradation;

        if ($asClass) {
            $this->withAttributes(['class' => $this->getH()]);
        }
        else {
            $this->tag($this->getH());
        }

        return $this;
    }
}
