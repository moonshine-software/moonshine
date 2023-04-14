<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use MoonShine\Resources\Resource;

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

    public function attributes(): array
    {
        return $this->hasResource()
            ? $this->getResource()->getFields()->extractLabels()
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

        if ($eager && $this->getResource()->hasWith()) {
            $model->load($this->getResource()->getWith());
        }

        if ($this->getResource()->softDeletes()) {
            $model->withTrashed();
        }

        $this->item = $model->find($this->getId());

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

    public function isRelatableMode(): bool
    {
        return $this->hasAny(['relatable_mode', 'redirect_back']) || $this->hasAny(['related_column', 'related_key']);
    }

    public function relatedColumn(): ?string
    {
        return $this->get('related_column');
    }

    public function relatedKey(): ?string
    {
        return $this->get('related_key');
    }

    public function redirectRoute(string $default): RedirectResponse
    {
        $redirectRoute = redirect($default);

        if ($this->isRelatableMode()) {
            $redirectRoute = back();
        }

        return $redirectRoute;
    }

    public function user($guard = null)
    {
        return parent::user($guard ?? 'moonshine');
    }
}
