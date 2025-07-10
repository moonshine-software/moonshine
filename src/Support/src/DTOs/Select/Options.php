<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;
use MoonShine\Support\Enums\ObjectFit;
use UnitEnum;

/**
 * @implements Arrayable<int|string, mixed>
 */
final readonly class Options implements Arrayable
{
    /**
     * @param  array<int|string,string|Option|OptionGroup|array<int|string,string>>  $values
     * @param  mixed|null  $value
     * @param  array<OptionProperty>|Closure  $properties
     */
    public function __construct(
        private array $values = [],
        private mixed $value = null,
        private array|Closure $properties = []
    ) {
    }

    /**
     * @return Collection<array-key, OptionGroup|Option>
     */
    public function getValues(): Collection
    {
        return (new Collection($this->values))
            ->filter()
            ->map(function (array|string|OptionGroup|Option $labelOrValues, int|string $valueOrLabel): OptionGroup|Option {
                if ($labelOrValues instanceof Option) {
                    return $labelOrValues;
                }

                $toOption = fn (string $label, string $value): Option => new Option(
                    label: $label,
                    value: $value,
                    selected: $this->isSelected($value),
                    properties: $this->getProperties($value),
                );

                if ($labelOrValues instanceof OptionGroup) {
                    return $labelOrValues;
                }

                if (\is_array($labelOrValues)) {
                    $options = [];

                    foreach ($labelOrValues as $value => $label) {
                        $options[] = $toOption($label, (string) $value);
                    }

                    return new OptionGroup(
                        label: (string) $valueOrLabel,
                        values: new Options($options)
                    );
                }

                return $toOption($labelOrValues, (string) $valueOrLabel);
            });
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getProperties(string $value): OptionProperty
    {
        $properties = data_get(value($this->properties), $value, []);

        if ($properties instanceof OptionProperty) {
            return $properties;
        }

        /** @var array{image: OptionImage} $properties */
        return new OptionProperty(
            ...$this->normalizeProperties($properties)
        );
    }

    /**
     * @throws JsonException
     */
    public function isSelected(string $value): bool
    {
        $current = $this->getValue();

        if ($current instanceof UnitEnum) {
            $current = $current->value ?? $current->name ?? null;
        }

        if (\is_string($current) && Str::of($current)->isJson()) {
            $current = json_decode(
                $current,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        if (is_iterable($current)) {
            return match (true) {
                $current instanceof Collection => $current->contains(
                    $value
                ),
                default => \in_array($value, (array) $current),
            };
        }

        if (! \is_scalar($current)) {
            return false;
        }

        return (string) $current === $value;
    }

    /**
     * @return array<mixed>
     */
    public function flatten(): array
    {
        return $this->getValues()
            ->mapWithKeys(static fn (OptionGroup|Option $optionOrGroup): array => $optionOrGroup instanceof OptionGroup
                ? $optionOrGroup->getValues()->flatten() :
                [$optionOrGroup->getValue() => $optionOrGroup])
            ->toArray();
    }

    /**
     * @return array<int|string, mixed>
     */
    public function toArray(): array
    {
        return $this->getValues()->toArray();
    }

    /**
     * @return array{options: array<mixed>, properties: array<mixed>}
     */
    public function toRaw(): array
    {
        $values = $this->getValues();

        $options = $values->mapWithKeys(function (Option|OptionGroup $option): array {
            if ($option instanceof OptionGroup) {
                return [$option->getLabel() => (new Collection($option->getValues()->toArray()))->pluck('label', 'value')->toArray()];
            }

            return [$option->getValue() => $option->getLabel()];
        })->toArray();

        $properties = (new Collection($this->flatten()))->pluck('properties', 'value')->toArray();

        return [
            'options' => $options,
            'properties' => $properties,
        ];
    }

    /**
     * @param  array{image: OptionImage}  $properties
     *
     * @return array{image: OptionImage}
     */
    private function normalizeProperties(array $properties): array
    {
        if (! isset($properties['image']) || $properties['image'] instanceof OptionImage) {
            return $properties;
        }

        $imageData = $properties['image'];

        if (\is_string($imageData)) {
            $properties['image'] = new OptionImage($imageData);

            return $properties;
        }

        /**
         * @var array{src: string, width: int, height: int, objectFit: null|string} $imageData
         */
        $properties['image'] = new OptionImage(
            $imageData['src'] ?? '',
            $imageData['width'] ?? null,
            $imageData['height'] ?? null,
            isset($imageData['objectFit']) ? ObjectFit::from($imageData['objectFit']) : ObjectFit::COVER
        );

        return $properties;
    }
}
