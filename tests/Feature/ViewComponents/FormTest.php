<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\ViewComponents;

use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Entities\ModelEntity;
use Leeto\MoonShine\Entities\ModelEntityBuilder;
use Leeto\MoonShine\ViewComponents\Form\Form;

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
        $valueEntity = (new ModelEntityBuilder($model))->build();

        $form = Form::make($this->testResource()->fieldsCollection()->formFields())
            ->fill($valueEntity);

        $this->assertInstanceOf(ModelEntity::class, $form->values());
    }
}
