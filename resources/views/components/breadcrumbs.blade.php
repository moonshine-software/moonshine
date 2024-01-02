@props([
    'items' => []
])
@if($items)
    <!-- Breadcrumbs -->
    <div {{ $attributes->class(['breadcrumbs', 'grow']) }}>
        <ul class="breadcrumbs-list">
            @foreach($items as $url => $title)
                <li class="breadcrumbs-item">
                    @if($loop->last && count($items) > 1)
                        <span>
                            {{ str($title)->before(':::') }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           @if(str($title)->contains(':::')) class="flex items-center justify-between gap-2" @endif
                        >
                            @if(str($title)->contains(':::'))
                                <x-moonshine::icon
                                    :icon="str($title)->after(':::')->value()"
                                    size="6"
                                />
                            @endif

                            {{ str($title)->before(':::') }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    <!-- END: Breadcrumbs -->
@endif
