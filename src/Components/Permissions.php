<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Checkbox;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label, ModelResource $resource, Model $item)
 */
final class Permissions extends MoonshineComponent
{
    use HasResource;
    use WithLabel;

    protected Model $item;

    protected $except = [
        'getItem',
        'getResource',
        'getForm',
    ];

    public function __construct(
        Closure|string $label,
        ModelResource $resource,
        Model $item
    ) {
        $this->setItem($item);
        $this->setResource($resource);
        $this->setLabel($label);
    }

    public function setItem(Model $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getItem(): Model
    {
        return $this->item;
    }

    public function getForm(): FormBuilder
    {
        $url = $this->getResource()
            ->route('permissions', $this->getItem()->getKey());

        $elements = [];
        $values = [];

        foreach (moonshine()->getResources() as $resource) {
            $elements[] = Heading::make($resource->title());
            $checkboxes = [];

            foreach($resource->gateAbilities() as $ability) {
                $values['permissions'][$resource::class][$ability] = $resource->isHaveUserPermission($this->getItem(), $ability);

                $checkboxes[] = Checkbox::make(
                    $ability,
                    "permissions." . $resource::class . ".$ability"
                )->setName("permissions[" . $resource::class . "][$ability]");
            }

            $elements[] = Flex::make($checkboxes);
        }

        return FormBuilder::make($url)
            ->fields($elements)
            ->fill($values)
            ->submit(__('moonshine::ui.save'));
    }

    public function render(): View|Closure|string
    {
        return view('moonshine::components.permissions', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'label' => $this->label(),
            'form' => $this->getForm(),
            'item' => $this->getItem(),
            'resource' => $this->getResource(),
        ]);
    }
}
