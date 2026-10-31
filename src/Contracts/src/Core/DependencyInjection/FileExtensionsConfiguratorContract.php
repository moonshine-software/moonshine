<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

/**
 * Global allowed file extensions for File and Image fields.
 * Kept separate from ConfiguratorContract so existing configurators stay compatible.
 */
interface FileExtensionsConfiguratorContract
{
    /**
     * @return string[]
     */
    public function getAllowedExtensions(): array;
}
