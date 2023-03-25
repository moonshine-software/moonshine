<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Contracts\Decorations\FieldsDecoration;
use Leeto\MoonShine\Contracts\ResourceRenderable;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithFields;
use Leeto\MoonShine\Traits\WithLabel;
use Leeto\MoonShine\Traits\WithView;

abstract class Decoration implements ResourceRenderable, FieldsDecoration
{
    use Makeable;
    use WithView;
    use WithLabel;
    use WithFields;

    public function __construct(string|array $labelOrFields, array $fields = [])
    {
        if (is_array($labelOrFields)) {
            $fields = $labelOrFields;
            $labelOrFields = '';
        }

        $this->setLabel($labelOrFields);
        $this->fields($fields);
    }

    /**
     * Get id of decoration
     *
     * @param  string|null  $index
     * @return string
     */
    public function id(string $index = null): string
    {
        return (string) str($this->label())->slug('_');
    }

    /**
     * Get name of decoration
     *
     * @param  string|null  $index
     * @return string
     */
    public function name(string $index = null): string
    {
        return $this->id($index);
    }
}
