<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Contracts\Decorations\FieldsDecoration;
use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithFields;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithUniqueId;
use MoonShine\Traits\WithView;

/**
 * @method static static make(string|array $labelOrFields = '', array $fields = [])
 */
abstract class Decoration implements ResourceRenderable, FieldsDecoration
{
    use Makeable;
    use WithView;
    use WithLabel;
    use WithFields;
    use WithUniqueId;

    public function __construct(
        string|array $labelOrFields = '',
        array $fields = []
    ) {
        if (is_array($labelOrFields)) {
            $fields = $labelOrFields;
            $labelOrFields = '';
        }

        $this->setLabel($labelOrFields);
        $this->fields($fields);
    }
}
