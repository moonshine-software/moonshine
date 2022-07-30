<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Fields\File;
use Leeto\MoonShine\Tests\TestCase;

class FileFieldTest extends TestCase
{
    public function test_make()
    {
        $field = File::make('Names');

        $this->assertEquals('names', $field->field());
        $this->assertEquals('names', $field->name());
        $this->assertEquals('names', $field->id());
        $this->assertNull($field->relation());
        $this->assertEquals('Names', $field->label());
    }

    public function test_allowed_extensions()
    {
        $field = File::make('Names');

        $this->assertTrue($field->isAllowedExtension('jpg'));

        $field->allowedExtensions(['docx']);

        $this->assertTrue($field->isAllowedExtension('docx'));
        $this->assertFalse($field->isAllowedExtension('jpg'));
    }
}
