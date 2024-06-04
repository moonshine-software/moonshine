<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Responses;

use Illuminate\Support\Traits\Conditionable;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\ToastType;
use MoonShine\Support\Traits\Makeable;
use Symfony\Component\HttpFoundation\JsonResponse;

/** @method static static make(array $data = []) */
final class MoonShineJsonResponse extends JsonResponse
{
    use Makeable;
    use Conditionable;

    protected array $jsonData = [];

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

    public function toast(string $value, ToastType $type = ToastType::DEFAULT): self
    {
        return $this->mergeJsonData([
            'message' => $value,
            'messageType' => $type->value,
        ]);
    }

    public function redirect(string $value): self
    {
        return $this->mergeJsonData(['redirect' => $value]);
    }

    public function events(array $events): self
    {
        return $this->mergeJsonData(['events' => AlpineJs::prepareEvents($events)]);
    }

    public function html(string $value): self
    {
        return $this->mergeJsonData(['html' => $value]);
    }

    /**
     * @param  array<string, string>  $value
     */
    public function fieldsValues(array $value): self
    {
        return $this->mergeJsonData(['fields_values' => $value]);
    }
}
