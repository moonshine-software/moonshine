<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Models;

class CustomKeyCategory extends Category
{
    protected $table = 'categories';

    protected $primaryKey = 'category_id';
}
