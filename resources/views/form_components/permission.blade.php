@if($item->exists && $item instanceof \Leeto\MoonShine\Models\MoonshineUser)
    <div>
        <div class="text-lg my-4">{{ $component->label() }}</div>

        <form method="post" action="{{ $resource->route('permissions', $item->getKey()) }}">
            @csrf

            @foreach(app(\Leeto\MoonShine\MoonShine::class)->getResources() as $resource)
                <div>
                    <div class="text-md my-4">{{ $resource->title() }}</div>

                    <div class="flex items-center justify-start space-x-4">
                        @foreach($resource->gateAbilities() as $ability)
                            <label class="flex items-center justify-between">
                                <input name="permissions[{{ get_class($resource) }}][{{ $ability }}]"
                                       type="checkbox"
                                       value="1"
                                    @checked(
                                        $item->moonshineUserPermission
                                        && $item->moonshineUserPermission->permissions->has(get_class($resource))
                                        && isset($item->moonshineUserPermission->permissions[get_class($resource)][$ability])
                                    )
                                >
                                <span class="ml-4">{{ $ability }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="py-10">
                @include('moonshine::base.form.shared.btn', [
                    'type' => 'submit',
                    'class' => 'form_submit_button',
                    'name' => trans('moonshine::ui.save')
                ])
            </div>
        </form>
    </div>
@endif
