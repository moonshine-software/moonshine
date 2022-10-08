<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Fields;

use Carbon\Carbon;
use Leeto\MoonShine\Fields\Date;
use Leeto\MoonShine\Tests\TestCase;

final class DateFieldTest extends TestCase
{
    protected string $format = 'd.m';

    /**
     * @test
     * @return void
     */
    public function it_date_format_with_carbon_now(): void
    {
        $field = Date::make('Date')
            ->setValue(now())
            ->format($this->format);

        $this->assertEquals(now()->format($this->format), $field->value());
    }

    /**
     * @test
     * @return void
     */
    public function it_date_format_with_timestamp(): void
    {
        $field = Date::make('Date')
            ->setValue(time())
            ->format($this->format);

        $this->assertEquals(date($this->format, time()), $field->value());
    }

    /**
     * @test
     * @return void
     */
    public function it_date_format_with_string(): void
    {
        $field = Date::make('Date')
            ->setValue('01.01.2000')
            ->format($this->format);

        $this->assertEquals('01.01', $field->value());
    }

    /**
     * @test
     * @return void
     */
    public function it_nullable_value(): void
    {
        $field = Date::make('Date')
            ->nullable();

        $this->assertNull($field->value());
    }

    /**
     * @test
     * @return void
     */
    public function it_valid_request_value(): void
    {
        request()->merge([
            'date' => time()
        ]);

        $field = Date::make('Date')
            ->format($this->format);

        $this->assertEquals(
            now()->format($this->format),
            $field->requestValue()->format($this->format)
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_nullable_request_value(): void
    {
        $field = Date::make('Date')
            ->nullable();

        $this->assertNull($field->requestValue());
    }

    /**
     * @test
     * @return void
     */
    public function it_callback_value(): void
    {
        $now = now()->addDay();

        $field = Date::make('Date', 'date', function (Carbon $value) {
            return $value->diffForHumans();
        })->setValue($now);

        $this->assertEquals($now->diffForHumans(), $field->value());
    }
}
