<?php

declare(strict_types=1);

namespace MoonShine\UI\Enums;

use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;

enum DisplayTextType: string
{
    case LABEL = 'label';

    case HINT = 'hint';

    case PREFIX = 'prefix';

    case SUFFIX = 'suffix';

    /** Text built from record data, escaped regardless of the global label default. */
    case RECORD = 'record';

    public function isEscapedBy(ConfiguratorContract $config): bool
    {
        return match ($this) {
            self::LABEL => $config->isEscapeLabel(),
            self::HINT => $config->isEscapeHint(),
            self::PREFIX => $config->isEscapePrefix(),
            self::SUFFIX => $config->isEscapeSuffix(),
            self::RECORD => true,
        };
    }
}
