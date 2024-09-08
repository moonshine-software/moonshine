<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use ArrayAccess;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\FormContract;
use MoonShine\Contracts\UI\LayoutContract;

/**
 * @template-covariant I of ConfiguratorContract
 * @mixin I
 */
interface ConfiguratorContract extends ArrayAccess
{
    public function getTitle(): string;

    /**
     * @return class-string<LayoutContract>
     */
    public function getLayout(): string;

    /**
     * @template-covariant T of FormContract
     * @param  class-string<T>  $default
     */
    public function getForm(string $name, string $default, mixed ...$parameters): FormBuilderContract;

    /**
     * @template-covariant T of PageContract
     * @param  class-string<T>  $default
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
