<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Page;

/**
 * Opt-in vertical spacing for a page's components().
 *
 * BaseLayout detects this marker and wraps the rendered page components in
 * Div.space-elements, leaving the page's component collection unchanged.
 * The title and subtitle stay outside the wrapper.
 *
 * A global default is also available via the content_spacing config
 * flag (off by default). See https://github.com/moonshine-software/moonshine/issues/2108.
 */
trait WithContentSpacing
{
}
