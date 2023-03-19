<!-- Breadcrumbs -->
<div class="breadcrumbs grow">
    <ul class="breadcrumbs-list">
        <li class="breadcrumbs-item">
            <a href="{{ route('moonshine.index') }}" rel="home">
                <x-moonshine::icon
                    icon="heroicons.home"
                    size="6"
                />
            </a>
        </li>

        @foreach($items as $url => $title)
            <li class="breadcrumbs-item">
                @if($loop->last)
                    <span>{{ $title }}</span>
                @else
                    <a href="{{ $url }}">{{ $title }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
<!-- END: Breadcrumbs -->
