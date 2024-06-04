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
        <div class="card-photo">
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
            @if(!is_array($thumbnail))
                <img src="{{ $thumbnail }}" alt="{{ $title }}" />
            @else
                <div class="card-photo-carousel" x-data='carousel(
                   @json($thumbnail)
                )' >
                    <template x-for="(slide, index) in slides">
                        <carousel-slide class="card-photo-carousel-slide" :class="(activeSlide === index) ? 'active' : ''">
                            <img :src="slide" alt="{{ $title }}">
                        </carousel-slide>
                    </template>
                    <div class="card-photo-carousel-navigation">
                        <a @click.prevent="previous" href="#" class="card-photo-carousel-navigation-next">
                            <x-moonshine::icon icon="heroicons.chevron-left" size="7"/>
                        </a>
                        <a @click.prevent="next" href="#" class="card-photo-carousel-navigation-prev">
                            <x-moonshine::icon icon="heroicons.chevron-right" size="7"/>
                        </a>
                    </div>
                    <div class="card-photo-carousel-slide-count">
                        <span x-text="activeSlide+1"></span> / {{count($thumbnail)}}
                    </div>
                </div>
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
