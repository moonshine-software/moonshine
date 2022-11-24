@include('moonshine::shared.btn', [
    'title' => $action->label(),
    'href' => $action->url(),
    'filled' => true,
    'icon' => 'app'
])
