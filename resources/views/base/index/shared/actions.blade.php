<div class="flex items-center select-none mt-5 space-x-4">
    @foreach($actions as $action)
        {!! $action->render() !!}
    @endforeach
</div>
