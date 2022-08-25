<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\Fields\HasRelatedValues;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Form\Form;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Tests\TestCase;

class FormTest extends TestCase
{
    protected Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = new MoonShineUserResource();
    }

    public function test_makeable()
    {
        $form = Form::make($this->resource->fieldsCollection()->formFields());

        $this->assertInstanceOf(Form::class, $form);
        $this->assertInstanceOf(Fields::class, $form->fields());
    }

    public function test_fill()
    {
        $model = MoonshineUser::query()->first();

        $form = Form::make($this->resource->fieldsCollection()->formFields())
            ->fill($model);

        $this->assertInstanceOf(MoonshineUser::class, $form->values());

        foreach ($form->fields()->onlyFields() as $field) {
            if ($field instanceof HasRelatedValues) {
                $this->assertInstanceOf(Collection::class, $field->relatedValues());
            }
        }
    }
}
