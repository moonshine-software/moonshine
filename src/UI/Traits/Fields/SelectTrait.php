<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Support\DTOs\Select\Options;

trait SelectTrait
{
    protected bool $native = false;

    protected array|Closure|Options $options = [];

    protected array|Closure $optionProperties = [];

    public function options(Closure|array|Options $data): static
    {
        $this->options = $data;

        return $this;
    }

    public function optionProperties(Closure|array $data): static
    {
        $this->optionProperties = $data;

        return $this;
    }

    public function getValues(): Options
    {
        if ($this->options instanceof Options) {
            return $this->options;
        }

        return new Options(
            value($this->options, $this),
            $this->getValue(),
            $this->optionProperties
        );
    }

    public function native(): static
    {
        $this->native = true;

        return $this;
    }

    protected function isNative(): bool
    {
        return $this->native;
    }
}
