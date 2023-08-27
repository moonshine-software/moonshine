<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Traits\Controller\InteractsWithAuth;
use MoonShine\Traits\Controller\InteractsWithUI;

abstract class MoonShineController extends BaseController
{
    use InteractsWithUI;
    use InteractsWithAuth;
}
