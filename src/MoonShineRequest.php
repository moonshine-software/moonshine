<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Stringable;
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
        return $this->getResource()->fieldsLabels();
    }

    public function getResource(): Resource
    {
        if ($this->resource) {
            return $this->resource;
        }

        $class = (string) str(request()->route()->getName())->betweenFirst('.', '.')
            ->singular()
            ->ucfirst()
            ->append('Resource')
            ->whenContains(
                ['MoonShine'],
                fn(Stringable $str) => $str->prepend('Leeto\MoonShine\Resources\\'),
                fn(Stringable $str) => $str->prepend(MoonShine::namespace('\Resources\\')),
            );

        $this->resource = new $class;

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
            ->with(array_values($this->getResource()->getAllRelations()))
            ->findOrFail($this->getId());

        return $this->model;
    }
}
