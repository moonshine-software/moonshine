@props([
    'items' => [],
    'portrait' => true,
    'alt' => '',
 ])
<div {{ $attributes->class(['carousel', 'portrait' => $portrait]) }}
     x-data='carousel(@json($items))'
>
    <template x-for="(slide, index) in slides">
        <carousel-slide class="carousel-slide" :class="(activeSlide === index) ? '_is-active' : ''">
            <img :src="slide" alt="{{ $alt }}">
        </carousel-slide>
    </template>

    <div class="carousel-navigation">
        <a @click.prevent="previous" href="javascript:void(0);" class="carousel-navigation-next" x-show="activeSlide !== 0">
            <x-moonshine::icon icon="chevron-left" />
        </a>
        <a @click.prevent="next" href="javascript:void(0);" class="carousel-navigation-prev" x-show="activeSlide !== {{ count($items) - 1 }}">
            <x-moonshine::icon icon="chevron-right" />
        </a>
    </div>
    <div class="carousel-slide-count">
        <span x-text="activeSlide+1"></span> / {{ count($items) }}
    </div>
</div>
