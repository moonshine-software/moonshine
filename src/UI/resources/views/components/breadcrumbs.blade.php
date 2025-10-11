@props([
    'items' => []
])
@if($items)
    <!-- Breadcrumbs -->
    <div {{ $attributes->class(['breadcrumbs']) }}>
        <ul class="breadcrumbs-list">
            @foreach($items as $url => $data)
                <li @class(['breadcrumbs-item', 'flex-shrink-0' => !empty($data['icon'])])>
                    @if($loop->last && count($items) > 1)
                        <span>
                            {{ $data['title'] }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           @if($data['icon']) class="flex items-center justify-between gap-2" @endif
                        >
                            @if($data['icon'])
                                <x-moonshine::icon
                                    :icon="$data['icon']"
                                    size="5"
                                />
                            @endif

                            {{ $data['title'] }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    <!-- END: Breadcrumbs -->
@endif
