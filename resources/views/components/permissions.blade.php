@if($item->exists && $resource->hasUserPermissions())
    <div>
        <div class="text-lg my-4">{{ $element->label() }}</div>

        <x-moonshine::form
            :action="$resource->route('permissions', $item->getKey())"
            method="post"
        >
            @foreach(moonshine()->getResources() as $resource)
                <div>
                    <div class="text-md my-4">{{ $resource->title() }}</div>

                    <div class="flex items-center justify-start space-x-4">
                        @foreach($resource->gateAbilities() as $ability)
                            <x-moonshine::form.input-wrapper
                                name="permissions[{{ get_class($resource) }}][{{ $ability }}]"
                                :label="$ability"
                                :beforeLabel="true"
                                class="form-group-inline"
                                :id="str('permissions_' . get_class($resource) . '_' . $ability)->slug('_')"
                            >
                                <x-moonshine::form.input
                                    :id="str('permissions_' . get_class($resource) . '_' . $ability)->slug('_')"
                                    type="checkbox"
                                    name="permissions[{{ get_class($resource) }}][{{ $ability }}]"
                                    value="1"
                                    :checked="$resource->isHaveUserPermission($item, $ability)"

                                />
                            </x-moonshine::form.input-wrapper>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <x-slot:buttons>
                <x-moonshine::form.button type="submit" class="form_submit_button">
                    {{ trans('moonshine::ui.save') }}
                </x-moonshine::form.button>
            </x-slot:buttons>
        </x-moonshine::form>
    </div>
@endif
