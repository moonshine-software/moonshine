<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Leeto\MoonShine\Resources\Resource;

class MoonShineRequest extends FormRequest
{
    protected ?Model $model = null;

    protected ?Resource $resource = null;

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

    public function getResource(): Resource
    {
        if ($this->resource) {
            return $this->resource;
        }

        $this->resource = MoonShine::getResourceFromUri($this->segment(3));

        return $this->resource;
    }

    public function getId(): ?string
    {
        return $this->route($this->getResource()->routeParam());
    }

    public function getModel(): Model
    {
        return $this->getResource()->getModel();
    }

    public function findModel(): ?Model
    {
        if ($this->model) {
            return $this->model;
        }

        $this->model = $this->getModel()
            ->newQuery()
            ->findOrFail($this->getId());

        return $this->model;
    }

    public function values(): array
    {
        return $this->getResource()
            ->fieldsCollection()
            ->requestValues()
            ->toArray();
    }
}
