<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Contracts\Decorations\FieldsDecoration;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Traits\WithFields;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithUniqueId;

/**
 * @method static static make(Closure|string|array $labelOrFields = '', array $fields = [])
 */
abstract class Decoration extends MoonShineComponent implements FieldsDecoration, HasFields
{
    use WithLabel;
    use WithFields;
    use WithUniqueId;

    public function __construct(
        Closure|string|array $labelOrFields = '',
        array $fields = []
    ) {
        if (is_array($labelOrFields)) {
            $fields = $labelOrFields;
            $labelOrFields = '';
        }

        $this->setLabel($labelOrFields);
        $this->fields($fields);
    }

    protected function viewData(): array
    {
        return [
            'element' => $this,
        ];
    }
}
