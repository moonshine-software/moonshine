<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\Fields\HasRelatedValues;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Form\Form;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Tests\TestCase;

class FormTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_makeable(): void
    {
        $form = Form::make($this->testResource()->fieldsCollection()->formFields());

        $this->assertInstanceOf(Form::class, $form);
        $this->assertInstanceOf(Fields::class, $form->fields());
    }

    /**
     * @test
     * @return void
     */
    public function it_fill(): void
    {
        $model = MoonshineUser::query()->first();

        $form = Form::make($this->testResource()->fieldsCollection()->formFields())
            ->fill($model);

        $this->assertInstanceOf(MoonshineUser::class, $form->values());

        foreach ($form->fields()->onlyFields() as $field) {
            if ($field instanceof HasRelatedValues) {
                $this->assertInstanceOf(Collection::class, $field->relatedValues());
            }
        }
    }
}
