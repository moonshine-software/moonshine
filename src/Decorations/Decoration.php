<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Decorations\FieldsDecoration;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithFields;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithUniqueId;
use MoonShine\Traits\WithView;

/**
 * @method static static make(Closure|string|array $labelOrFields = '', array $fields = [])
 */
abstract class Decoration implements MoonShineRenderable, FieldsDecoration, HasFields
{
    use Makeable;
    use WithView;
    use WithLabel;
    use WithFields;
    use WithUniqueId;
    use WithComponentAttributes;
    use Conditionable;

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

    public function render(): View|Closure|string
    {
        return view($this->getView(), [
            'element' => $this,
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
