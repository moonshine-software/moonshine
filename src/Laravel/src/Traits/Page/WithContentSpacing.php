<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Page;

use MoonShine\Core\Collections\Components;
use MoonShine\Laravel\Layouts\BaseLayout;
use MoonShine\UI\Components\Layout\Div;

/**
 * Opt-in vertical spacing for a page's components().
 *
 * Wraps getComponents() in Div.space-elements so sibling blocks get the
 * same rhythm already used in Box, Form and Tabs. The page title and
 * subtitle stay outside: the layout renders them, not getComponents().
 *
 * A global default is also available via the content_spacing config
 * flag (off by default). See https://github.com/moonshine-software/moonshine/issues/2108.
 */
trait WithContentSpacing
{
    public function getComponents(): Components
    {
        $components = parent::getComponents();

        if ($components->isEmpty() || BaseLayout::isContentSpacingWrapped($components)) {
            return $components;
        }

        return Components::make([
            Div::make($components)
                ->class('space-elements')
                ->name(BaseLayout::CONTENT_SPACING_NAME),
        ]);
    }

}
