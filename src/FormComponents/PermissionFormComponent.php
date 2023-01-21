<?php

declare(strict_types=1);

namespace Leeto\MoonShine\FormComponents;

use Leeto\MoonShine\Helpers\Condition;
use Leeto\MoonShine\Traits\Fields\HasCanSee;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithView;

final class PermissionFormComponent extends FormComponent
{
    protected static string $view = 'moonshine::form_components.permission';
}
