<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use BackedEnum;
use Closure;
use Illuminate\Support\Collection;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Stringify;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\FlexibleRender;
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
                    ? Stringify::value($value->toString())
                    : (string) $value->value,
            ])->all()
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
                $enum = $callback();

                return $enum instanceof BackedEnum ? $enum : null;
            } catch (Throwable) {
            }

            return null;
        };

        if ($this->attached !== null && (\is_int($value) || \is_string($value))) {
            $value = $rescueEnum(fn () => $this->attached::tryFrom($value)) ?? $value;
        }

        if ($this->isMultiple()) {
            $enumClass = $this->attached;

            if ($enumClass === null) {
                return parent::resolvePreview();
            }

            $badged = false;
            $values = Collection::wrap($value)->map(function ($v) use ($enumClass, $rescueEnum, &$badged): string {
                if (! \is_int($v) && ! \is_string($v)) {
                    return '';
                }

                $enum = $rescueEnum(fn () => $enumClass::tryFrom($v)) ?? $enumClass::tryFrom((int) $v);
                $result = (string) ($enum->value ?? $v);

                if ($enum !== null && method_exists($enum, 'toString')) {
                    $result = Stringify::value($enum->toString());
                }

                if ($enum !== null && method_exists($enum, 'getColor')) {
                    $badged = true;

                    /** @var string|Color $color */
                    $color = $enum->getColor();

                    return (string) Badge::make($result, $color, method_exists($enum, 'getIcon') ? Stringify::value($enum->getIcon()) : null);
                }

                return $result;
            });


            if ($badged) {
                $this->isBadge = false;
            }

            return (string) Flex::make([
                FlexibleRender::make($this->getMultiplePreview($values, $badged ? '' : ',')),
            ])->unwrap()->withoutSpace()->class('gap-1');
        }

        if (\is_scalar($value)) {
            return Stringify::value(data_get(
                $this->getValues(),
                (string) $value,
                (string) $value
            ));
        }

        if (! $value instanceof BackedEnum) {
            return '';
        }

        if (method_exists($value, 'getColor')) {
            /** @var string|Color $color */
            $color = $value->getColor();
            $this->badge(
                $color,
                method_exists($value, 'getIcon') ? Stringify::value($value->getIcon()) : null
            );
        }

        if (method_exists($value, 'toString')) {
            return Stringify::value($value->toString());
        }

        return (string) ($value->value ?? '');
    }
}
