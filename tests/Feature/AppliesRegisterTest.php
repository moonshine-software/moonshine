<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Applies\Fields\FileModelApply;
use MoonShine\Laravel\Applies\Filters\JsonModelApply;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;

uses()->group('core');
uses()->group('applies');

beforeEach(function () {
    $this->appliesRegister = $this->moonshineCore->getContainer(AppliesRegisterContract::class);
});

it('apply finder', function (): void {
    $file = File::make('File');
    $image = Image::make('Image');
    $json = Json::make('Json');

    /** @var FileModelApply $fileApply */
    $fileApply = $file->getApplyClass();
    /** @var FileModelApply $imageApply */
    $imageApply = $image->getApplyClass();
    $jsonApply = $json->getApplyClass();
    $jsonFilterApply = $json->getApplyClass('filters', ModelResource::class);

    expect($fileApply)
        ->toBeInstanceOf(FileModelApply::class)
        ->and($imageApply)
        ->toBeInstanceOf(FileModelApply::class)
        ->and($jsonApply)
        ->toBeNull()
        ->and($jsonFilterApply)
        ->toBeInstanceOf(JsonModelApply::class);

    $upload = UploadedFile::fake()->create('avatar.png');

    expect($fileApply->store($file, $upload))
        ->toBe($upload->hashName());
});

it('add new field apply', function (): void {
    $this->appliesRegister
        ->type('fields')
        ->for(ModelResource::class)
        ->add(Text::class, CustomTextFieldApply::class);

    $field = Text::make('Column');

    $this->post('/', ['column' => '!']);

    $data = $field->apply(static fn (array $data) => $data, [
        'column' => 'hello',
    ]);

    expect($data)
        ->toBe(['column' => 'hello world!']);
});

it('add new filter apply', function (): void {
    $this->appliesRegister
        ->type('filters')
        ->for(ModelResource::class)
        ->add(Text::class, CustomTextFilterApply::class);

    $field = Text::make('Column');

    $this->post('/', ['column' => '!']);

    $filterApply = $this->appliesRegister->findByField($field, 'filters');

    $defaultApply = static fn (Builder $query): Builder => $query->where(
        $field->getColumn(),
        $field->getRequestValue()
    );

    if (! \is_null($filterApply)) {
        $field->onApply($filterApply->apply($field));
    }

    $data = $field->apply($defaultApply, (new Item())->newQuery());

    expect($data->toSql())
        ->toContain('some_column')
        ->and($data->getBindings())
        ->toContain('!');
});

class CustomTextFilterApply implements ApplyContract
{
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $q, string $value, Field $field) {
            return $q->where('some_column', $value);
        };
    }
}

class CustomTextFieldApply implements ApplyContract
{
    public function apply(FieldContract $field): Closure
    {
        return static function (array $data, string $value, Field $field) {
            $data[$field->getColumn()] .= ' world' . $value;

            return $data;
        };
    }
}
