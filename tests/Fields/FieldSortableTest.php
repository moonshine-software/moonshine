<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fields;

use MoonShine\Fields\Text;
use MoonShine\Tests\FakeRequests;
use MoonShine\Tests\TestCase;

class FieldSortableTest extends TestCase
{
    use FakeRequests;

    /**
     * @test
     * @return void
     */
    public function it_sortable_disable(): void
    {
        $field = Text::make('Field');

        $this->assertFalse($field->isSortable());
    }

    /**
     * @test
     * @return void
     */
    public function it_sortable_enable(): void
    {
        $field = Text::make('Field')->sortable();

        $this->assertTrue($field->isSortable());
    }

    /**
     * @test
     * @return void
     */
    public function it_sort_type(): void
    {
        $field = Text::make('Field')->sortable();

        $this->fakeRequest(parameters: [
            'order' => [
                'type' => 'desc',
                'field' => $field->field()
            ]
        ]);


        $this->assertTrue($field->sortType('desc'));

        $this->fakeRequest(parameters: [
            'order' => [
                'type' => 'asc',
                'field' => $field->field()
            ]
        ]);

        $this->assertTrue($field->sortType('asc'));
    }

    /**
     * @test
     * @return void
     */
    public function it_sort_query(): void
    {
        $field = Text::make('Field')->sortable();

        $this->fakeRequest(parameters: [
            'order' => [
                'type' => 'asc',
                'field' => $field->field()
            ]
        ]);

        $this->assertEquals(
            request()->url() . '/?' . http_build_query([
                'order' => [
                    'field' => $field->field(),
                    'type' => 'desc'
                ]
            ]),
            $field->sortQuery()
        );
    }
}
