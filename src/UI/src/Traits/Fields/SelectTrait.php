<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;
use MoonShine\Support\DTOs\Select\Option;
use MoonShine\Support\DTOs\Select\OptionGroup;
use MoonShine\Support\DTOs\Select\OptionProperty;
use MoonShine\Support\DTOs\Select\Options;

trait SelectTrait
{
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
        if ($this->options instanceof Options && empty($this->getValue())) {
            return $this->options;
        }

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

    /**
     * @throws JsonException
     */
    protected function getMultiplePreview(mixed $value): string
    {
        $value = \is_string($value) && Str::of($value)->isJson() ?
            json_decode($value, true, 512, JSON_THROW_ON_ERROR)
            : $value;

        /** @var Collection<array-key, int|string> $collection */
        $collection = new Collection($value);

        return $collection
            ->when(
                ! $this->isRawMode(),
                fn (Collection $collect): Collection => $collect->map(
                    fn (int|string $v): string => (string)data_get($this->getValues()->flatten(), "$v.label", ''),
                ),
            )
            ->implode(',');
    }
}
