<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Decorations\FieldsDecoration;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithFields;
use MoonShine\Traits\WithHtmlAttributes;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithUniqueId;
use MoonShine\Traits\WithView;

/**
 * @method static static make(string|array $labelOrFields = '', array $fields = [])
 */
abstract class Decoration implements MoonShineRenderable, FieldsDecoration
{
    use Makeable;
    use WithView;
    use WithLabel;
    use WithFields;
    use WithUniqueId;
    use WithHtmlAttributes;

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

    public function render(): View|Closure|string
    {
        return view($this->getView(), [
            'element' => $this,
        ]);
    }

    public function __toString()
    {
        return (string) $this->render();
    }
}
