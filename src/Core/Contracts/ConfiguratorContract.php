<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use ArrayAccess;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Contracts\Forms\FormContract;
use MoonShine\UI\MoonShineLayout;

interface ConfiguratorContract extends ArrayAccess
{
    public function getTitle(): string;

    /**
     * @return class-string<MoonShineLayout>
     */
    public function getLayout(): string;

    /**
     * @template-covariant T of FormContract
     * @param  class-string<T>  $default
     */
    public function getForm(string $name, string $default, mixed ...$parameters): FormBuilder;

    /**
     * @template-covariant T of PageContract
     * @param  class-string<T>  $default
     * @return PageContract
     */
    public function getPage(string $name, string $default, mixed ...$parameters): PageContract;

    /**
     * @return string[]
     */
    public function getLocales(): array;

    public function getDisk(): string;

    /**
     * @return string[]
     */
    public function getDiskOptions(): array;
}
