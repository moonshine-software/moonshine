@if($resource->isRelatable() || request('relatable_mode'))
    <x-moonshine::form.input
        type="hidden"
        name="relatable_mode"
        value="1"
    />
@endif

@if(request()->routeIs('*.query-tag'))
    <x-moonshine::form.input
        type="hidden"
        name="redirect_back"
        value="1"
    />
@endif
