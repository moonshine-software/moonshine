<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use App\MoonShine\Resources\CommentResource;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\FormButton;
use MoonShine\Buttons\IndexPage\MassDeleteButton;
use MoonShine\Buttons\IndexPage\ShowButton;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Traits\WithFields;
use Throwable;

class HasMany extends ModelRelationField implements HasFields
{
    use WithFields;

    protected bool $isGroup = true;

    protected bool $outsideComponent = true;

    public function resolveFill(
        array $raw = [],
        mixed $casted = null,
        int $index = 0
    ): Field {

        if(!$this->toOne()) {
            $casted = $this->resolveCasted($casted);
        }

        return parent::resolveFill(
            $raw,
            $casted,
            $index
        );
    }

    protected function resolveCasted(mixed $casted): mixed
    {
        if(is_null($casted)) {
            return null;
        }

        if(!$casted instanceof Model) {
            return $casted;
        }

        $this->getResource()
            ->resolveQuery()
            ->where(
                $casted->{$this->getRelationName()}()->getForeignKeyName(),
                $casted->{$casted->getKeyName()}
            );

        $relationItems = $this->getResource()->paginate();

        $relationItems->setPath(to_relation_search_route(request()->input()));

        $casted->setRelation($this->getRelationName(), $relationItems);

        return $casted;
    }

    /**
     * @throws Throwable
     */
    public function preparedFields(): Fields
    {
        if (! $this->hasFields()) {
            $fields = $this->toOne()
                ? $this->getResource()->getFormFields()
                : $this->getResource()->getIndexFields();

            $this->fields($fields->toArray());

            return Fields::make($this->fields);
        }

        return $this->getFields()->when(
            $this->toOne(),
            static fn (Fields $fields): Fields => $fields->formFields(),
            static fn (Fields $fields): Fields => $fields->indexFields()
        );
    }

    protected function resolvePreview(): View|string
    {
        $items = $this->toValue() ?? [];

        if($this->toOne()) {
            $items = Arr::wrap($items);
        }

        if ($this->isRawMode()) {
            return $items
                ->map(fn (Model $item) => $item->{$this->getResourceColumn()})
                ->implode(';');
        }

        $resource = $this->getResource();

        return TableBuilder::make(items: $items)
            ->fields($this->preparedFields())
            ->cast($resource->getModelCast())
            ->preview()
            ->simple()
            ->when(
                $this->toOne(),
                static fn (TableBuilder $table): TableBuilder => $table->vertical()
            )
            ->render();
    }

    protected function resolveValue(): mixed
    {
        /**
         * @var CommentResource $resource
         */
        $resource = $this->getResource();

        $asyncUrl = to_relation_route('search-relations', request('resourceItem'));

        $items = $this->toValue() ?? [];

        if(!empty($items) && !$items instanceof Paginator) {
            $items = $resource->paginateItems($items, $asyncUrl);
        }

        return TableBuilder::make(items: $items)
            ->async($asyncUrl)
            ->name($this->getRelationName())
            ->fields($this->preparedFields())
            ->cast($resource->getModelCast())
            ->when(
                $this->isNowOnForm(),
                fn (TableBuilder $table): TableBuilder => $table->withNotFound()
            )
            ->buttons([
                ShowButton::for($resource),
                FormButton::for($resource),
                DeleteButton::for($resource, request()->getUri()),
                MassDeleteButton::for($resource),
            ]);
    }
}
