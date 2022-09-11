<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Contracts\Decorations\FieldsDecoration;

final class InlineBlock extends Decoration implements FieldsDecoration
{
    public static string $component = 'InlineBlockDecoration';
}
