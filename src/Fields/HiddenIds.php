<?php

declare(strict_types=1);

namespace MoonShine\Fields;

/**
 * @method static static make(?string $forComponent = null)
 */
class HiddenIds extends Field
{
    protected string $view = 'moonshine::fields.hidden-ids';

    protected string $type = 'hidden';

    //TODO 3.0 Make $forComponent argument required
    public function __construct(
        protected ?string $forComponent = null
    ) {
        if($this->forComponent !== null && $this->forComponent !== '' && $this->forComponent !== '0') {
            $this->customAttributes([
                'data-for-component' => $this->forComponent,
            ]);
        }

        parent::__construct();
    }
}
