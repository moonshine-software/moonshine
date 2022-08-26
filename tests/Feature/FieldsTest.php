<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Tests\TestCase;

class FieldsTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_only_fields(): void
    {
        foreach ($this->testResource()->fieldsCollection()->onlyFields() as $field) {
            $this->assertNotInstanceOf(Decoration::class, $field);
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_table_fields(): void
    {
        foreach ($this->testResource()->fieldsCollection()->tableFields() as $field) {
            $this->assertIsBool($field->isOnIndex());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_form_fields(): void
    {
        foreach ($this->testResource()->fieldsCollection()->formFields()->onlyFields() as $field) {
            $this->assertIsBool($field->isOnForm());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_export_fields(): void
    {
        foreach ($this->testResource()->fieldsCollection()->exportFields() as $field) {
            $this->assertIsBool($field->isOnExport());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_extract_labels(): void
    {
        $labels = $this->testResource()->fieldsCollection()->extractLabels();

        $this->assertArrayHasKey('id', $labels);
        $this->assertArrayHasKey('name', $labels);
    }
}
