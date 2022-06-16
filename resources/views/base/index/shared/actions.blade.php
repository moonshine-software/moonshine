<div class="flex items-center select-none mt-5">
    @foreach($actions as $action)
        @include('moonshine::shared.btn', [
            'title' => $action->label(),
            'href' => $action->url(),
            'filled' => true,
            'icon' => 'app'
        ])
    @endforeach
</div>