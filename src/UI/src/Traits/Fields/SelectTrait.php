<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Support\DTOs\Select\Option;
use MoonShine\Support\DTOs\Select\OptionGroup;
use MoonShine\Support\DTOs\Select\OptionProperty;
use MoonShine\Support\DTOs\Select\Options;

trait SelectTrait
{
    protected bool $native = false;

    /**
     * @var array<int|string,string|Option|OptionGroup|array<int|string,string>>|Closure|Options
     */
    protected array|Closure|Options $options = [];

    /**
     * @var array<OptionProperty>|Closure
     */
    protected array|Closure $optionProperties = [];

    /**
     * @param  Closure|array<int|string,string|Option|OptionGroup|array<int|string,string>>|Options  $data
     *
     * @return $this
     */
    public function options(Closure|array|Options $data): static
    {
        $this->options = $data;

        return $this;
    }

    /**
     * @param  Closure|array<OptionProperty>  $data
     */
    public function optionProperties(Closure|array $data): static
    {
        $this->optionProperties = $data;

        return $this;
    }

    public function getValues(): Options
    {
        if ($this->options instanceof Options) {
            ['options' => $options, 'properties' => $properties] = $this->options->toRaw();

            return new Options(
                $options,
                $this->getValue(),
                $properties
            );
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
