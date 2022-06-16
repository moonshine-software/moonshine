<style>
    .snap {
        -ms-scroll-snap-type: x mandatory;
        scroll-snap-type: x mandatory;
        -ms-overflow-style: none;
        scroll-behavior: smooth
    }
    .snap::-webkit-scrollbar {
        display: none;
    }

    .snap > div {
        scroll-snap-align: center;
    }
</style>
<div x-data="{items: [{{$values}}], active: 0}">
    <div class="snap overflow-auto relative flex-no-wrap flex transition-all" x-ref="slider"
         x-on:scroll.debounce="active = Math.round($event.target.scrollLeft / ($event.target.scrollWidth / items.length))">
        <template x-for="(item,index) in items" :key="index">
            <div class="w-full flex-shrink-0 h-32 bg-black text-white flex items-center justify-center">
                <img @click.stop="$dispatch('img-modal', {imgModal: true, imgModalSrc: item })" :src="item" />
            </div>
        </template>
    </div>
    <div class="p-4 flex items-center justify-center flex-1 bg-purple bg-opacity-75">
        <button class="outline-none focus:outline-none rounded-full mx-4 text-white"
                x-on:click=" $refs.slider.scrollLeft = $refs.slider.scrollLeft - ($refs.slider.scrollWidth / items.length)">
            <svg class="w-6 h-6 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <template x-for="(item,index) in items" :key="index">
            <span class="bg-white w-3 h-3 block mx-1 bg-opacity-25 shadow rounded-full"
                  x-bind:class="{'bg-opacity-100': active === index }"></span>
        </template>

        <button class="outline-none focus:outline-none rounded-full mx-4 text-white"
                x-on:click="$refs.slider.scrollLeft = $refs.slider.scrollLeft + ($refs.slider.scrollWidth / items.length)">
            <svg class="w-6 h-6 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</div>