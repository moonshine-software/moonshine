<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithHeadingGradation
{
    protected string $gradation = '3';

    public function getH(): string
    {
        return "h$this->gradation";
    }

    public function h(string|int $gradation = '3', $asClass = true): static
    {
        $this->gradation = preg_replace('/[^0-9]/','', (string) $gradation);

        if ($asClass) {
            $this->withAttributes(['class' => $this->getH()]);
        }
        else {
            $this->tag($this->getH());
        }

        return $this;
    }
}
