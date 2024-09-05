<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

/**
 * @method static static make(?string $forComponent = null)
 */
class HiddenIds extends Field
{
    protected string $view = 'moonshine::fields.hidden-ids';

    protected string $type = 'hidden';

    public function __construct(
        protected string $forComponent
    ) {
        parent::__construct();

        $this->customAttributes([
            'data-for-component' => $this->forComponent,
        ]);
    }
}
