<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Foundation\Http\FormRequest;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Contracts\ValueEntityContract;

class MoonShineRequest extends FormRequest
{
    protected mixed $data = null;

    protected ?ValueEntityContract $valueEntity = null;

    protected ?ResourceContract $resource = null;

    protected ?string $viewClass = null;

    protected ?string $viewComponentClass = null;

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return trans('moonshine::validation');
    }

    public function attributes(): array
    {
        return $this->getResource()
            ->fieldsCollection()
            ->extractLabels();
    }

    public function getResource(): ResourceContract
    {
        if ($this->resource) {
            return $this->resource;
        }

        $this->resource = MoonShine::getResourceFromUriKey($this->segment(3));

        return $this->resource;
    }

    public function getViewClass(): ?string
    {
        if ($this->viewClass) {
            return $this->viewClass;
        }

        if (!$this->getResource()) {
            return null;
        }

        $this->viewClass = $this->getResource()->views()->findByUriKey(
            $this->segment(4)
        );

        return $this->viewClass;
    }

    public function getViewComponentClass(): ?string
    {
        if ($this->viewComponentClass) {
            return $this->viewComponentClass;
        }

        if (!$this->getResource()) {
            return null;
        }

        if (!$this->getViewClass()) {
            return null;
        }

        $this->viewComponentClass = str($this->segment(5))
            ->studly()
            ->singular()
            ->value();

        return $this->viewComponentClass;
    }

    public function getId(): ?string
    {
        return $this->route($this->getResource()->routeParam())
            ?? $this->route('id');
    }

    public function getDataInstance()
    {
        return $this->getResource()->getDataInstance();
    }

    public function getData()
    {
        if ($this->data) {
            return $this->data;
        }

        $this->data = $this->getResource()
            ->getData($this->getId());

        return $this->data;
    }

    public function getDataOrFail()
    {
        if ($this->data) {
            return $this->data;
        }

        $this->data = $this->getData();

        abort_if(is_null($this->data), 404);

        return $this->data;
    }

    public function getValueEntity(): ValueEntityContract
    {
        if ($this->valueEntity) {
            return $this->valueEntity;
        }

        $this->valueEntity = $this->getResource()->valueEntity(
            $this->getDataOrFail()
        );

        return $this->valueEntity;
    }

    public function values(string $prefix = null): array
    {
        return $this->getResource()
            ->fieldsCollection()
            ->requestValues($prefix)
            ->toArray();
    }
}
