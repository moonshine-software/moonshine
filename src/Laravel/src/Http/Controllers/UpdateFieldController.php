<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Http\Response;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Exceptions\CrudResourceException;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Exceptions\ModelRelationFieldException;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelColumnUpdateRequest;
use MoonShine\Laravel\Http\Requests\Resources\UpdateColumnFormRequest;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use Throwable;

class UpdateFieldController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function throughColumn(UpdateColumnFormRequest $request): Response
    {
        return $this->save($request->getResourceOrFail(), $request->getField() ?? throw CrudResourceException::resourceOrFieldRequired());
    }

    /**
     * @throws Throwable
     */
    public function throughRelation(RelationModelColumnUpdateRequest $request): Response
    {
        $relationField = $request->getField();

        if (! $relationField instanceof HasFieldsContract) {
            throw ModelRelationFieldException::hasFieldsContractRequired();
        }

        $relationField->getPreparedFields();

        $resource = $relationField->getResource();

        $field = $relationField
            ->getFields()
            ->onlyFields()
            ->findByColumn($request->string('field')->value());

        if (\is_null($field) || \is_null($resource)) {
            throw CrudResourceException::resourceOrFieldRequired();
        }

        return $this->save($resource, $field);
    }

    private function save(CrudResource $resource, FieldContract $field): Response
    {
        abort_unless(
            $field instanceof HasUpdateOnPreviewContract && $field->isUpdateOnPreview(),
            403
        );

        try {
            $resource->save(
                $resource->getCaster()->cast(
                    $resource->getItemOrFail()
                ),
                Fields::make([$field])
            );
        } catch (ResourceException $e) {
            throw_if(! moonshine()->isProduction(), $e);
            report_if(moonshine()->isProduction(), $e);

            return response($e->getMessage());
        }

        return response()->noContent();
    }
}
