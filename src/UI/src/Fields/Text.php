<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\Support\Renderable;
use JsonException;
use MoonShine\Support\Enums\TextWrap;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithEscapedValue;
use MoonShine\UI\Traits\Fields\WithInputExtensions;
use MoonShine\UI\Traits\Fields\WithMask;

class Text extends Field implements HasDefaultValueContract, CanBeString, HasUpdateOnPreviewContract
{
    use WithInputExtensions;
    use WithMask;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;
    use WithEscapedValue;

    protected string $view = 'moonshine::fields.input';

    protected ?TextWrap $textWrap = TextWrap::ELLIPSIS;

    protected string $type = 'text';

    /**
     * @throws JsonException
     */
    public function tags(?int $limit = null): static
    {
        return $this
            ->setAttribute('type', 'hidden')
            ->xDataMethod(
                'select',
                null,
                json_encode([
                    'create' => true,
                    'persist' => true,
                    'createOnBlur' => true,
                    'maxItems' => $limit,
                    'mode' => 'multi',
                ], JSON_THROW_ON_ERROR),
                json_encode([
                    'max_items' => [],
                    'caret_position' => [],
                    'input_autogrow' => [],
                    'restore_on_backspace' => [],
                ], JSON_THROW_ON_ERROR)
            )
            ->customAttributes([
                'data-search-enabled' => true,
                'data-remove-item-button' => true,
            ]);
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        if (\is_string($value)) {
            return $this->isUnescape() ? $value : $this->escapeValue($value);
        }

        return $value;
    }

    protected function resolvePreview(): Renderable|string
    {
        return $this->isUnescape()
            ? parent::resolvePreview()
            : $this->escapeValue((string) parent::resolvePreview());
    }

    protected function viewData(): array
    {
        return [
            ...$this->getExtensionsViewData(),
        ];
    }
}
