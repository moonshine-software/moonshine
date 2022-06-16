<nav class="mt-10">
    @if($data)
        @foreach($data as $item)
            @if($item->isGroup())
                @include("moonshine::components.shared.menu-group", ["item" => $item])
            @else
                @include("moonshine::components.shared.menu-item", ["item" => $item])
            @endif
        @endforeach
    @endif
</nav>