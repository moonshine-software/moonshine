<?php

declare(strict_types=1);

namespace MoonShine\UI\Enums;

use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\DisplayEscapingConfiguratorContract;
use MoonShine\Contracts\UI\HasHintEscapingContract;
use MoonShine\Contracts\UI\HasLabelEscapingContract;
use MoonShine\Contracts\UI\HasLinkNameEscapingContract;
use MoonShine\Contracts\UI\HasRenderEscapingContract;
use MoonShine\Contracts\UI\HasTitleEscapingContract;

enum DisplayTextType: string
{
    case LABEL = 'label';

    case HINT = 'hint';

    case PREFIX = 'prefix';

    case SUFFIX = 'suffix';

    case BEFORE_RENDER = 'before_render';

    case AFTER_RENDER = 'after_render';

    /** Modal and off-canvas titles. */
    case TITLE = 'title';

    /** Field link names, often derived from field values. */
    case LINK_NAME = 'link_name';

    /** Labels of related items. */
    case OPTION_LABEL = 'option_label';

    /**
     * Whether the default can be changed in the configuration.
     * Other types are built from record data and stay escaped unless changed locally.
     */
    public function isConfigurable(): bool
    {
        return match ($this) {
            self::LABEL, self::HINT, self::PREFIX, self::SUFFIX, self::BEFORE_RENDER, self::AFTER_RENDER => true,
            default => false,
        };
    }

    public function isEscapedBy(ConfiguratorContract $config): bool
    {
        // Configurators without escaping settings keep the safe default.
        if (! $this->isConfigurable() || ! $config instanceof DisplayEscapingConfiguratorContract) {
            return true;
        }

        return match ($this) {
            self::LABEL => $config->isEscapeLabel(),
            self::HINT => $config->isEscapeHint(),
            self::PREFIX => $config->isEscapePrefix(),
            self::SUFFIX => $config->isEscapeSuffix(),
            self::BEFORE_RENDER => $config->isEscapeBeforeRender(),
            self::AFTER_RENDER => $config->isEscapeAfterRender(),
            default => true,
        };
    }

    /**
     * Resolves the preference of an owner that may not implement the escaping contracts,
     * such as a third-party field, falling back to the default for this type.
     */
    public function isEscapedFor(object $owner, ConfiguratorContract $config): bool
    {
        return match (true) {
            $this === self::LABEL && $owner instanceof HasLabelEscapingContract => $owner->isEscapeLabel(),
            $this === self::HINT && $owner instanceof HasHintEscapingContract => $owner->isEscapeHint(),
            $this === self::TITLE && $owner instanceof HasTitleEscapingContract => $owner->isEscapeTitle(),
            $this === self::LINK_NAME && $owner instanceof HasLinkNameEscapingContract => $owner->isEscapeLinkName(),
            $this === self::BEFORE_RENDER && $owner instanceof HasRenderEscapingContract => $owner->isEscapeBeforeRender(),
            $this === self::AFTER_RENDER && $owner instanceof HasRenderEscapingContract => $owner->isEscapeAfterRender(),
            default => $this->isEscapedBy($config),
        };
    }
}
