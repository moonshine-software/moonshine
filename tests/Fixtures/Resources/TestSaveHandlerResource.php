<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Crud\Attributes\SaveHandler;
use MoonShine\Tests\Fixtures\Services\SaveItemHandler;

#[SaveHandler(SaveItemHandler::class)]
class TestSaveHandlerResource extends TestResource
{
}
