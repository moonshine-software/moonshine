<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use ArrayAccess;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\FormContract;
use MoonShine\Contracts\UI\LayoutContract;

/**
 * @template-covariant I of ConfiguratorContract = ConfiguratorContract
 * @mixin I
 * @extends ArrayAccess<string, mixed>
 */
interface ConfiguratorContract extends ArrayAccess
{
    public function getNamespace(string $path = '', ?string $base = null): string;

    public function getTitle(): string;

    /**
     * @return class-string<LayoutContract>
     */
    public function getLayout(): string;

    /**
     * @return class-string<PaletteContract>
     */
    public function getPalette(): string;

    /**
     * @template T of FormContract
     * @param  class-string<T>  $default
     */
    public function getForm(string $name, string $default, mixed ...$parameters): FormBuilderContract;

    /**
     * @template T of PageContract
     * @param  class-string<T>  $default
     *
     * @return T
     */
    public function getPage(string $name, string $default, mixed ...$parameters): PageContract;

    /**
     * @return list<class-string<PageContract>>
     */
    public function getPages(): array;

    /**
     * @return string[]
     */
    public function getLocales(): array;

    public function getLocale(): string;

    public function getLocaleKey(): string;

    /**
     * @return string
     */
    public function getDisk(): string;

    /**
     * @return string[]
     */
    public function getDiskOptions(): array;
}
