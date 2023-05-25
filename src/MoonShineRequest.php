<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use MoonShine\Resources\Resource;
use Throwable;

class MoonShineRequest extends FormRequest
{
    protected ?Model $item = null;

    protected ?Resource $resource = null;

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return trans('moonshine::validation');
    }

    /**
     * @throws Throwable
     */
    public function attributes(): array
    {
        return $this->hasResource()
            ? $this->getResource()->getFields()
                ->formFields()
                ->extractLabels()
            : [];
    }

    public function getResourceUri(): ?string
    {
        if (trim(config('moonshine.route.prefix', ''), '/') === '') {
            return $this->segment(2);
        }

        return $this->segment(3);
    }

    public function hasResource(): bool
    {
        return str($this->url())->contains('resource/');
    }

    public function getResource(): Resource
    {
        if ($this->resource) {
            return $this->resource;
        }

        $this->resource = MoonShine::getResourceFromUriKey($this->getResourceUri());

        if ($this->getId()) {
            $this->resource->setItem(
                $this->getItemOrFail()
            );
        }

        return $this->resource;
    }

    public function getIdBySegment(): ?string
    {
        return $this->segment(4);
    }

    public function getId(): ?string
    {
        return $this->route($this->getResource()->routeParam())
            ?? $this->route('id');
    }

    public function getItemOrInstance(bool $eager = false): Model
    {
        return $this->getItem($eager) ?? $this->getResource()->getModel();
    }

    public function getItem(bool $eager = false): ?Model
    {
        if ($this->item) {
            return $this->item;
        }

        if (! $this->getId()) {
            return null;
        }

        $model = $this->getResource()->getModel();

        if ($this->getResource()->softDeletes()) {
            $model = $model->withTrashed();
        }

        $this->item = $model->find($this->getId());

        if ($this->item && $eager && $this->getResource()->hasWith()) {
            $this->item->load($this->getResource()->getWith());
        }

        return $this->item;
    }

    public function getItemOrFail(bool $eager = false): ?Model
    {
        if ($this->item) {
            return $this->item;
        }

        $this->item = $this->getItem($eager);

        abort_if(is_null($this->item), 404);

        return $this->item;
    }

    public function getIndexParameter(): ?string
    {
        return $this->route('index');
    }

    public function redirectRoute(string $default): RedirectResponse
    {
        $redirectRoute = redirect($default);

        if ($this->isRelatableMode()) {
            $redirectRoute = back();
        }

        return $redirectRoute;
    }

    public function isRelatableMode(): bool
    {
        return $this->getResource()->isRelatable()
            || $this->has('relatable_mode');
    }

    public function user($guard = null)
    {
        return parent::user($guard ?? 'moonshine');
    }
}
