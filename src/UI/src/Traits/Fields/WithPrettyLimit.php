<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\PrettyLimit;

trait WithPrettyLimit
{
    /**
     * @param  string|Color|(Closure(string|int|null $value, static $ctx): null|Color|string)|null  $color = null
     * @param  string|(Closure(string|int|null $value, static $ctx): null|string)|null  $label = null
     * @param  int|(Closure(string|int|null $value, static $ctx): null|int)|null  $limit = null
     *
     */
    public function prettyLimit(
        null|Color|string|Closure $color = null,
        null|string|Closure $label = null,
        null|int|Closure $limit = null,
    ): static {
        return $this->changePreview(
            static function (mixed $value, self $ctx) use ($color, $label, $limit): string {
                $value = \is_int($value) || $value === null ? $value : $ctx->stringifySlotContent($value);

                return $value ? (string) PrettyLimit::make(
                    value: (string) $value,
                    color: $color instanceof Closure ? $color($value, $ctx) : $color,
                    label: $label instanceof Closure ? $label($value, $ctx) : $label,
                    limit: $limit instanceof Closure ? $limit($value, $ctx) : $limit,
                )
                : (string) $value;
            }
        );
    }
}
