@if($item->exists && $item instanceof \Leeto\MoonShine\Models\MoonshineUser)
    <div>
        <div class="text-lg my-4">{{ $element->label() }}</div>

        <x-moonshine::form
            :action="$resource->route('permissions', $item->getKey())"
            method="post"
        >

            @foreach(app(\Leeto\MoonShine\MoonShine::class)->getResources() as $resource)
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
                                    :checked="($item->moonshineUserPermission
                                        && $item->moonshineUserPermission->permissions->has(get_class($resource))
                                        && isset($item->moonshineUserPermission->permissions[get_class($resource)][$ability])
                                    )"

                                />
                            </x-moonshine::form.input-wrapper>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <x-slot:button type="submit" class="form_submit_button">
                {{ trans('moonshine::ui.save') }}
            </x-slot:button>
        </x-moonshine::form>
    </div>
@endif
