<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Views;

use JsonSerializable;
use Leeto\MoonShine\ViewComponents\Form\Form;
use Leeto\MoonShine\Contracts\ResourceContract;
use Leeto\MoonShine\Traits\Makeable;

final class FormView implements JsonSerializable
{
    use Makeable;

    public function __construct(
        protected ResourceContract $resource,
        protected Form $form
    ) {
    }

    /**
     * @return ResourceContract
     */
    public function resource(): ResourceContract
    {
        return $this->resource;
    }

    /**
     * @return Form
     */
    public function form(): Form
    {
        return $this->form;
    }


    public function jsonSerialize(): array
    {
        return [
            'title' => $this->resource()->title(),
            'resource' => [
                'uriKey' => $this->resource()->uriKey(),
                'id' => $this->form()->values()?->id(),
                'form' => $this->form()
            ]
        ];
    }
}
