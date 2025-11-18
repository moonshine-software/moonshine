<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Support\DTOs\Select\Option;
use MoonShine\Support\DTOs\Select\OptionGroup;
use MoonShine\Support\DTOs\Select\OptionImage;
use MoonShine\Support\DTOs\Select\OptionProperty;
use MoonShine\Support\DTOs\Select\Options;
use MoonShine\Support\Enums\ObjectFit;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Select;

uses()->group('fields');
uses()->group('selects');

beforeEach(function (): void {
    $this->selectOptions = [
        0 => 1,
        1 => 2,
        2 => 3,
    ];

    $this->field = Select::make('Select')->options($this->selectOptions);

    $this->fieldMultiple = Select::make('Select multiple')
        ->options($this->selectOptions)
        ->multiple();

    $this->item = new class () extends Model {
        public int $select = 1;
        public array $select_multiple = [1];

        protected $casts = [
            'select_multiple' => 'json',
        ];
    };

    fillFromModel($this->field, $this->item);
    fillFromModel($this->fieldMultiple, $this->item);
});

describe('basic methods', function () {
    it('type', function (): void {
        expect($this->field->getAttributes()->get('type'))
            ->toBeEmpty();
    });

    it('view', function (): void {
        expect($this->field->getView())
            ->toBe('moonshine::fields.select');
    });

    it('preview', function (): void {
        expect((string) $this->field->previewMode())
            ->toBe('2')
            ->and((string) $this->fieldMultiple->withoutWrapper())
            ->toBe(
                view('moonshine::fields.select', $this->fieldMultiple->toArray())->render()
            );
    });

    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('default value', function () {
        $field = Select::make('Select')->options([
            1 => 1,
            2 => 2,
        ])->default(2);

        expect($field->toValue())
            ->toBe(2);
    });

    it('applies', function () {
        expect()
            ->applies($this->field);
    });

    it('multiple', function (): void {
        expect($this->field->isMultiple())
            ->toBeFalse()
            ->and($this->fieldMultiple->isMultiple())
            ->toBeTrue();
    });

    it('searchable', function (): void {
        expect($this->fieldMultiple)
            ->isSearchable()
            ->toBeFalse()
            ->and($this->fieldMultiple->searchable())
            ->isSearchable()
            ->toBeTrue();
    });

    it('options', function (): void {
        expect($this->fieldMultiple)
            ->getValues()->toArray()
            ->toBe((new Options(
                $this->selectOptions,
                [1]
            ))->toArray());
    });

    it('is selected correctly', function (): void {
        expect($this->fieldMultiple)
            ->getValues()
            ->isSelected('1')
            ->toBeTrue();
    });

    it('is selected invalid', function (): void {
        expect($this->fieldMultiple)
            ->getValues()
            ->isSelected('2')
            ->toBeFalse();
    });

    it('is selected grouped correctly', function (): void {
        expect(Select::make('Select')->options(
            ['Group 1' => [1 => 1], 'Group 2' => [2 => 2]]
        )->default(2)->toValue())
            ->toBe(2);
    });

    it('names single', function (): void {
        expect($this->field)
            ->getNameAttribute()
            ->toBe('select')
            ->getNameAttribute('1')
            ->toBe('select');
    });

    it('names multiple', function (): void {
        expect($this->fieldMultiple)
            ->getNameAttribute()
            ->toBe('select_multiple[]')
            ->getNameAttribute('1')
            ->toBe('select_multiple[1]');
    });

    it('apply', function (): void {
        $data = ['select' => 1];

        fakeRequest(method: 'post', parameters: $data);

        expect(
            $this->field->apply(
                TestResourceBuilder::new()->fieldApply($this->field),
                new class () extends Model {
                    protected $fillable = [
                        'select',
                    ];
                }
            )
        )
            ->toBeInstanceOf(Model::class)
            ->select
            ->toBe($data['select'])
        ;
    });

    it('apply multiple', function (): void {
        $data = ['select' => [1,2]];

        fakeRequest(method: 'post', parameters: $data);

        expect(
            $this->field->apply(
                TestResourceBuilder::new()->fieldApply($this->field),
                new class () extends Model {
                    protected $fillable = [
                        'select',
                    ];
                }
            )
        )
            ->toBeInstanceOf(Model::class)
            ->select
            ->toBe($data['select'])
        ;
    });
});

describe('select field with images', function () {
    it('by array', function () {
        $field = Select::make('Select field with images')
            ->options([
                1 => 'Option 1',
                2 => 'Option 2',
            ])
            ->optionProperties(fn () => [
                1 => ['image' => 'image1.jpg'],
                2 => ['image' => 'image2.png'],
            ]);

        $result = $field->toArray();

        expect($result['values'][1]['properties']['image'])->toBe([
            'src' => 'image1.jpg',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ])->and($result['values'][2]['properties']['image'])->toBe([
            'src' => 'image2.png',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ]);
    });

    it('array with OptionImage object', function () {
        $field = Select::make('Select field with images')
            ->options([
                1 => 'Option 1',
                2 => 'Option 2',
            ])
            ->optionProperties(fn () => [
                1 => ['image' => new OptionImage('image1.jpg', 6, 6, ObjectFit::FILL)],
                2 => ['image' => new OptionImage('image2.png')],
            ]);

        $result = $field->toArray();

        expect($result['values'][1]['properties']['image'])->toBe([
            'src' => 'image1.jpg',
            'width' => 6,
            'height' => 6,
            'objectFit' => ObjectFit::FILL->value,
        ])->and($result['values'][2]['properties']['image'])->toBe([
            'src' => 'image2.png',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ]);
    });

    it('objects with image string', function () {
        $options = new Options([
            new Option(
                'Option 1',
                '1',
                properties: new OptionProperty(new OptionImage('image1.jpg'))
            ),
            new Option(
                'Option 2',
                '2',
                true,
                properties: new OptionProperty('image2.png')
            ),
            new OptionGroup('Group', new Options([
                new Option(
                    'Option 3',
                    '3',
                    true,
                    properties: new OptionProperty(new OptionImage('image3.png'))
                ),
            ])),
        ]);

        $field = Select::make('Select field with images')
            ->options($options);

        $result = $field->toArray();

        expect($result['values'][0]['selected'])->toBeFalse();
        expect($result['values'][1]['selected'])->toBeTrue();
        expect($result['values'][2]['values'][0]['selected'])->toBeTrue();

        expect($result['values'][0]['properties']['image'])->toBe([
            'src' => 'image1.jpg',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ])->and($result['values'][1]['properties']['image'])->toBe([
            'src' => 'image2.png',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ])->and($result['values'][2]['values'][0]['properties']['image'])->toBe([
            'src' => 'image3.png',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ]);

        $field = Select::make('Select field with images')
            ->setValue(1)
            ->options($options);

        $result = $field->toArray();

        // keys is option value

        expect($result['values'][1]['selected'])->toBeTrue();
        expect($result['values'][2]['selected'])->toBeFalse();
        expect($result['values']['Group']['values'][0]['selected'])->toBeFalse();

        expect($result['values'][1]['properties']['image'])->toBe([
            'src' => 'image1.jpg',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ])->and($result['values'][2]['properties']['image'])->toBe([
            'src' => 'image2.png',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ])->and($result['values']['Group']['values'][0]['properties']['image'])->toBe([
            'src' => 'image3.png',
            'width' => 10,
            'height' => 10,
            'objectFit' => ObjectFit::COVER->value,
        ]);
    });

    it('only objects', function () {
        $options = new Options([
            new Option(
                'Option 1',
                '1',
                properties: new OptionProperty(
                    new OptionImage(
                        src: 'image1.jpg',
                        width: 5,
                        height: 5,
                        objectFit: ObjectFit::COVER
                    )
                )
            ),
            new Option(
                'Option 2',
                '2',
                properties: new OptionProperty(
                    new OptionImage(
                        src: 'image2.png',
                        width: 8,
                        objectFit: ObjectFit::CONTAIN
                    )
                )
            ),
            new OptionGroup('Group', new Options([
                new Option(
                    'Option 3',
                    '3',
                    properties: new OptionProperty(
                        new OptionImage(
                            src: 'image3.png',
                            width: 8,
                            objectFit: ObjectFit::CONTAIN
                        )
                    )
                ),
            ])),
        ]);

        $field = Select::make('Select field with images')
            ->options($options)
            ->default('2')
            ->multiple();

        $result = $field->toArray();

        expect($result['values'][1]['properties']['image'])->toBe([
            'src' => 'image1.jpg',
            'width' => 5,
            'height' => 5,
            'objectFit' => ObjectFit::COVER->value,
        ])->and($result['values'][2]['properties']['image'])->toBe([
            'src' => 'image2.png',
            'width' => 8,
            'height' => 10,
            'objectFit' => ObjectFit::CONTAIN->value,
        ])->and($result['values']['Group']['values'][0]['properties']['image'])->toBe([
            'src' => 'image3.png',
            'width' => 8,
            'height' => 10,
            'objectFit' => ObjectFit::CONTAIN->value,
        ]);
    });

    it('handles empty images', function () {
        $options = new Options([
            new Option(
                'Option 1',
                '1',
                properties: new OptionProperty()
            ),
        ]);

        $field = Select::make('Select field with images')
            ->options($options);

        $result = $field->toArray();

        expect($result['values'][0]['properties']['image'])->toBeNull();
    });
});
