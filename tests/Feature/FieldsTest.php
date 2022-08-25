<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Tests\TestCase;

class FieldsTest extends TestCase
{
    protected Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = new MoonShineUserResource();
    }

    public function test_only_fields()
    {
        foreach ($this->resource->fieldsCollection()->onlyFields() as $field) {
            $this->assertNotInstanceOf(Decoration::class, $field);
        }
    }

    public function test_table_fields()
    {
        foreach ($this->resource->fieldsCollection()->tableFields() as $field) {
            $this->assertIsBool($field->isOnIndex());
        }
    }

    public function test_form_fields()
    {
        foreach ($this->resource->fieldsCollection()->formFields()->onlyFields() as $field) {
            $this->assertIsBool($field->isOnForm());
        }
    }

    public function test_export_fields()
    {
        foreach ($this->resource->fieldsCollection()->exportFields() as $field) {
            $this->assertIsBool($field->isOnExport());
        }
    }

    public function test_extract_labels()
    {
        $labels = $this->resource->fieldsCollection()->extractLabels();

        $this->assertArrayHasKey('id', $labels);
        $this->assertArrayHasKey('name', $labels);
    }
}
