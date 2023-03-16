<ul class="dropdown-menu">
    @foreach($actions as $action)
        <li class="dropdown-menu-item">
            {!! $action->render() !!}
        </li>
    @endforeach
</ul>
