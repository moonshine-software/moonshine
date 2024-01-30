@if(count($metrics))
    <div class="pb-10">
        <div class="flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10">
            @foreach($metrics as $metric)
                {!! $resource->renderComponent($metric, $resource->getModel()) !!}
            @endforeach
        </div>
    </div>
@endif
