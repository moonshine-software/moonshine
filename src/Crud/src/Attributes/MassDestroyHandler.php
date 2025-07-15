<?php

declare(strict_types=1);

namespace MoonShine\Crud\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class MassDestroyHandler
{
    /**
     * @param  class-string  $service
     * @param  ?string  $method
     */
    public function __construct(
        public string $service,
        public ?string $method = null,
    ) {
    }
}
