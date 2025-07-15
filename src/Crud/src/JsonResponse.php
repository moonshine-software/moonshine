<?php

declare(strict_types=1);

namespace MoonShine\Crud;

use Illuminate\Support\Traits\Conditionable;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\ToastType;
use MoonShine\Support\Traits\Makeable;
use MoonShine\UI\Enums\HtmlMode;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

/** @method static static make(array $data = []) */
class JsonResponse extends SymfonyJsonResponse
{
    use Makeable;
    use Conditionable;

    /**
     * @var array<string, mixed>
     */
    protected array $jsonData = [];

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct();

        $this->mergeJsonData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function mergeJsonData(array $data): self
    {
        $this->jsonData = array_filter(
            array_merge($this->jsonData, $data)
        );

        return $this->setData($this->jsonData);
    }

    public function toast(string $value, ToastType $type = ToastType::DEFAULT, null|int|false $duration = null): self
    {
        return $this->mergeJsonData([
            'message' => $value,
            'messageType' => $type->value,
            'messageDuration' => $duration === false ? -1 : $duration,
        ]);
    }

    public function redirect(string $value): self
    {
        return $this->mergeJsonData(['redirect' => $value]);
    }

    /**
     * @param  string[]  $events
     */
    public function events(array $events): self
    {
        return $this->mergeJsonData(['events' => AlpineJs::prepareEvents($events)]);
    }

    /**
     * @param  string|array<string, string>  $value
     */
    public function html(string|array $value, HtmlMode $mode = HtmlMode::INNER_HTML): self
    {
        if (\is_string($value)) {
            return $this->htmlData($value, mode: $mode);
        }

        foreach ($value as $selector => $html) {
            $this->htmlData($html, $selector, $mode);
        }

        return $this;
    }

    /**
     * @param  string|array<string, string>  $value
     */
    public function htmlData(string|array $value, ?string $selector = null, HtmlMode $mode = HtmlMode::INNER_HTML): self
    {
        if (! isset($this->jsonData['htmlData'])) {
            $this->jsonData['htmlData'] = [];
        }

        $this->jsonData['htmlData'][] = [
            'html' => $value,
            'selector' => $selector ?? '',
            'htmlMode' => $mode->value,
        ];

        return $this->setData($this->jsonData);
    }

    /**
     * @api
     *
     * @param  array<string, string>  $value
     */
    public function fieldsValues(array $value): self
    {
        return $this->mergeJsonData(['fields_values' => $value]);
    }
}
