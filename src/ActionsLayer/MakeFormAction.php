<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ActionsLayer;

use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\ViewComponents\Form\Form;

final class MakeFormAction
{
    public function __invoke(
        ResourceContract $resource,
        ?ValueEntityContract $value = null
    ): Form {
        $form = Form::make($resource->fieldsCollection()->formFields())
            ->action($resource->route('store'))
            ->method('post');

        if ($value) {
            $form->fill($value)
                ->action($resource->route('update', $value->id()))
                ->method('put');
        }

        return $form;
    }
}
