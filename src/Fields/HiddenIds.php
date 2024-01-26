<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class HiddenIds extends Field
{
    protected string $view = 'moonshine::fields.hidden-ids';

    protected string $type = 'hidden';

    //TODO Make $forComponent parameter required
    public function __construct(
        protected ?string $forComponent = null
    ) {
        if(! empty($this->forComponent)) {
            $this->customAttributes([
                'data-for-component' => $this->forComponent,
            ]);
        }

        parent::__construct();
    }
}
