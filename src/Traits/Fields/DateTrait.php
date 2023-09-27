<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

trait DateTrait
{
    protected string $format = 'Y-m-d H:i:s';

    protected string $inputFormat = 'Y-m-d';

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getInputFormat(): string
    {
        return $this->inputFormat;
    }

    public function withTime(): static
    {
        $this->type = "datetime-local";
        $this->inputFormat = "Y-m-d\TH:i";

        return $this;
    }
}
