<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Responses;

use JsonSerializable;
use Leeto\MoonShine\Form\Form;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Makeable;

final class ResourceForm implements JsonSerializable
{
    use Makeable;

    public function __construct(
        protected Resource $resource,
        protected Form $form
    ) {
    }

    /**
     * @return Resource
     */
    public function resource(): Resource
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
                'id' => $this->form()->values()?->getKey(),
                'policies' => $this->resource()->policies($this->form()->values()),
                'form' => $this->form()
            ]
        ];
    }
}
