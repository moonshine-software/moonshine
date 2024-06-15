@props([
    'url' => '#',
    'title' => '',
    'subtitle' => '',
    'thumbnail' => '',
    'overlay' => false,
    'values' => [],
    'header' => null,
    'actions' => null,
])
<!-- Card -->
<div {{ $attributes->class(['card', 'card-category']) }}>
    @if($thumbnail)
        <div class="@if(is_array($thumbnail)) card-carousel @else card-photo @endif">
            @if($overlay)
                {{ $header ?? '' }}

                <div class="card-photo-content">
                    <h3 class="title"><a href="{{ $url }}">{{ $title }}</a></h3>

                    @if($subtitle)
                        <div class="subcategory">
                            {{ $subtitle }}
                        </div>
                    @endif
                </div>
            @endif

            @if(is_array($thumbnail))
                <x-moonshine::carousel :items="$thumbnails" :alt="$title"></x-moonshine::carousel>
            @else
                <img src="{{ $thumbnail }}" alt="{{ $title }}" />
            @endif
        </div>
    @endif

    <div class="card-body">
        @if(!$overlay && $title)
            {{ $header ?? '' }}

            <h3 class="title"><a href="{{ $url }}">{{ $title }}</a></h3>

            @if($subtitle)
                <div class="subcategory">
                    {{ $subtitle }}
                </div>
            @endif
        @endif

        @if($values)
            <table>
                <tbody>
                @foreach($values as $label => $value)
                    <tr>
                        <th width="40%">{{ $label }}:</th>
                        <td width="60%">{!! $value !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        {{ $slot }}
    </div>

    @if($actions?->isNotEmpty())
        <div {{ $actions->attributes->class(['card-actions']) }}>
            {{ $actions }}
        </div>
    @endif
</div>
<!-- END: Card -->
