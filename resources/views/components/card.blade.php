@props([
    'url' => '#',
    'title' => '',
    'subtitle' => '',
    'thumbnail' => '',
    'overlay' => false,
    'values' => [],
    'header',
    'actions'
])
<!-- Card -->
<div {{ $attributes->class(['card', 'card-category']) }}>
    @if($thumbnail)
        <div class="card-photo">
            @if($overlay)
                {{ $header ?? '' }}

                <div class="card-photo-content">
                    <h3 class="title"><a href="{{ $url }}">{{ $title }}</a></h3>

                    @if($subtitle)
                        <div class="subcategory">
                            {{ $subtitle }}</a>
                        </div>
                    @endif
                </div>
            @endif

            <img src="{{ $thumbnail }}" alt="{{ $title }}" />
        </div>
    @endif

    <div class="card-body">
        @if(!$overlay && $title)
            {{ $header ?? '' }}

            <h3 class="title">{{ $title }}</h3>
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

    @if($actions ?? false)
        <div {{ $actions->attributes->class(['card-actions']) }}>
            {{ $actions }}
        </div>
    @endif
</div>
<!-- END: Card -->
