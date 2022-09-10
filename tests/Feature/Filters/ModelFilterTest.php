<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Filters\ModelFilter;
use Leeto\MoonShine\Tests\TestCase;

final class ModelFilterTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function it_apply_filter(): void
    {
        request()->merge([
            'filters' => [
                'name' => $this->adminUser()->name,
                'email' => $this->adminUser()->email
            ]
        ]);

        $filter = ModelFilter::make(
            'Test',
            [
                Text::make('Name'),
                Text::make('Email')
            ],
            function (Builder $query, $value) {
                return $query->where('name', $value['name']);
            }
        );

        $apply = $filter->apply($this->adminUser()->newQuery())->first();

        $this->assertTrue($this->adminUser()->is($apply));
    }
}
