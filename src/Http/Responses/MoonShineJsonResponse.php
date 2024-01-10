<?php

declare(strict_types=1);

namespace MoonShine\Http\Responses;

use Illuminate\Support\Traits\Conditionable;
use MoonShine\Enums\ToastType;
use MoonShine\Traits\Makeable;
use Symfony\Component\HttpFoundation\JsonResponse;

/** @method static static make(array $data = []) */
final class MoonShineJsonResponse extends JsonResponse
{
    use Makeable;
    use Conditionable;

    protected array $jsonData = [
        'messageType' => 'default'
    ];

    public function __construct(array $data = [])
    {
        parent::__construct();

        $this->mergeJsonData($data);
    }

    protected function mergeJsonData(array $data): self
    {
        $this->jsonData = array_filter(
            array_merge($this->jsonData, $data)
        );

        return $this->setData($this->jsonData);
    }

    public function message(string $value): self
    {
        return $this->mergeJsonData(['message' => $value]);
    }

    public function type(string|ToastType $value): self
    {
        return $this->mergeJsonData([
            'messageType' => is_string($value)
                ? $value
                : $value->value
        ]);
    }

    public function redirect(string $value): self
    {
        return $this->mergeJsonData(['redirect' => $value]);
    }

    public function html(string $value): self
    {
        return $this->mergeJsonData(['html' => $value]);
    }

    /**
     * @param  array<string, string>  $value
     * @return self
     */
    public function fieldsValues(array $value): self
    {
        return $this->mergeJsonData(['fields_values' => $value]);
    }
}
