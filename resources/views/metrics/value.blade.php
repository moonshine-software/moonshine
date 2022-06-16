<x-moonshine::block title="{{ $item->label() }}">
    @if($item->isProgress())
        <div class="flex items-center">
            <div class="flex items-center place-content-center w-14 h-14">
                <svg class="transform -rotate-90 w-14 h-14">
                    <circle class="text-gray opacity-25" cx="28" cy="28" r="22" stroke="currentColor" stroke-width="4" fill="transparent"></circle>
                    <circle class="text-purple" cx="28" cy="28" r="22" stroke="currentColor" stroke-width="4" fill="transparent" stroke-dasharray="138.28571428571428" stroke-dashoffset="0"></circle>
                </svg>
                <span class="absolute text-md font-bold text-purple">{{ $item->valueResult() }}</span>
            </div>
            <span class="text-white text-md ml-3">%</span>
        </div>
    @else
        <div class="text-2xl font-bold">
            {{ $item->valueResult() }}
        </div>
    @endif
</x-moonshine::block>
