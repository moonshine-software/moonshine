<?php

declare(strict_types=1);

namespace MoonShine\PHPStan;

use MoonShine\Core\Traits\NowOn;
use MoonShine\Crud\Traits\WithComponentsPusher;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Resource\ResourceWithParent;
use MoonShine\Support\Concerns\MenuFillerConcern;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\Support\Traits\WithQueue;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Traits\Fields\HasTabModeConcern;

// Analysis-only consumers: PHPStan checks traits in the context of a using class.
// Keep these in package checks too, where consumers in other packages are absent.
abstract class NowOnContext
{
    use NowOn;
}

abstract class ComponentsPusherContext
{
    use WithComponentsPusher;
}

abstract class ParentResourceContext extends ModelResource
{
    use ResourceWithParent;
}

abstract class MenuFillerContext
{
    use MenuFillerConcern;
}

abstract class ComponentAttributesContext extends Field
{
    use WithComponentAttributes;
}

abstract class QueueContext
{
    use WithQueue;
}

abstract class TabModeContext extends Field
{
    use HasTabModeConcern;
}
