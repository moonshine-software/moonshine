<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use BackedEnum;
use Closure;
use Illuminate\Support\Collection;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeEnum;
use Throwable;

class Enum extends Select implements CanBeEnum
{
    /** @var class-string<BackedEnum>|null */
    protected ?string $attached = null;

    /**
     * @param  class-string<BackedEnum>  $class
     */
    public function attach(string $class): static
    {
        $this->attached = $class;

        $values = new Collection($class::cases());

        $this->options(
            $values->mapWithKeys(static fn ($value): array => [
                $value->value => method_exists($value, 'toString')
                    ? $value->toString()
                    : $value->value,
            ])->toArray()
        );

        return $this;
    }

    protected function resolveRawValue(): mixed
    {
        return $this->resolvePreview();
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if (\is_null($value)) {
            return '';
        }

        $rescueEnum = static function (Closure $callback): BackedEnum|null {
            try {
                return $callback();
            } catch (Throwable) {
            }

            return null;
        };

        if ($this->attached !== null && ! $value instanceof $this->attached) {
            $value = $rescueEnum(fn () => $this->attached::tryFrom($value)) ?? $value;
        }

        if ($this->isMultiple()) {
            $badged = false;
            $values = $value->map(function ($v) use ($rescueEnum, &$badged) {
                $enum = $rescueEnum(fn () => $this->attached::tryFrom($v)) ?? $this->attached::tryFrom((int) $v);
                $result = $enum ? (string) ($enum->value ?? '') : $v;

                if (method_exists($enum, 'toString')) {
                    $result = (string) $enum->toString();
                }

                if (method_exists($enum, 'getColor')) {
                    $badged = true;

                    return (string) Badge::make($result, $enum->getColor(), method_exists($enum, 'getIcon') ? $enum->getIcon() : null);
                }

                return $result;
            });


            if ($badged) {
                $this->isBadge = false;
            }

            return (string) Flex::make([
                $this->getMultiplePreview($values, $badged ? '' : ','),
            ])->unwrap()->withoutSpace()->class('gap-1');
        }

        if (\is_scalar($value)) {
            return data_get(
                $this->getValues(),
                $value,
                (string) $value
            );
        }

        if (method_exists($value, 'getColor')) {
            $this->badge(
                $value->getColor(),
                method_exists($value, 'getIcon') ? $value->getIcon() : null
            );
        }

        if (method_exists($value, 'toString')) {
            return (string) $value->toString();
        }

        return (string) ($value->value ?? '');
    }
}
