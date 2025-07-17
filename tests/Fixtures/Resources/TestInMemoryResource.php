<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\UI\Fields\Field;

final class TestInMemoryResource extends CrudResource
{
    protected ?string $casterKeyName = 'id';

    public array $items = [];

    public array $fields = [];

    protected function indexFields(): iterable
    {
        return $this->fields;
    }

    protected function formFields(): iterable
    {
        return $this->indexFields();
    }

    public function findItem(bool $orFail = false): ?DataWrapperContract
    {
        $item = $this->getItems()->firstWhere('id', $this->getItemID());

        if ($item === null && $orFail) {
            throw new \Exception('Item not found');
        }

        return $this->getCaster()->cast($item);
    }

    public function getItems(): Collection
    {
        return new Collection(
            $this->items,
        );
    }

    public function massDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->delete(
                $this->getCaster()->cast(
                    $this->getItems()->firstWhere('id', $id)
                )
            );
        }
    }

    public function delete(DataWrapperContract $item, ?FieldsContract $fields = null): bool
    {
        $this->items = array_values(
            array_filter($this->items, static fn (array $el) => $el['id'] !== $item->getKey())
        );

        return true;
    }

    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract
    {
        $this->isRecentlyCreated = $item->getKey() !== null;

        $fields ??= $this->getFormFields()->onlyFields(withApplyWrappers: true);

        $fields->fill($item->toArray(), $item);

        $result = $item->getOriginal();

        $fields->onlyFields()->each(function (Field $field) use (&$result) {
            $result = $field->beforeApply($result);
        });

        $fields->onlyFields()->each(function (Field $field) use (&$result) {
            $result = $field->apply(
                fn (array $data, mixed $value, Field $ctx) => data_set($data, $ctx->getColumn(), $value),
                $result
            );
        });

        $fields->onlyFields()->each(function (Field $field) use (&$result) {
            $result = $field->afterApply($result);
        });

        $this->setItem($result);

        return $this->getCaster()->cast(
            $result
        );
    }
}
