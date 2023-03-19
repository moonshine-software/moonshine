<div class="flex items-center gap-2">
@foreach($actions as $action)
    {!! $action->render() !!}
@endforeach
</div>
