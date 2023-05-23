<x-moonshine::column
    :colSpan="$element->columnSpanValue()"
    :adaptiveColSpan="$element->adaptiveColumnSpanValue()" xmlns:x-moonshine="http://www.w3.org/1999/html"
>
    <x-moonshine::box
        class="box-shadow zoom-in h-full p-0"
    >
        <div class="report-card">
            <div class="report-card-heading">
                {!! $element->getIcon(6, 'pink') !!}
            </div>

            @if($element->isProgress())
                <x-moonshine::progress-bar
                    color="purple"
                    :radial="false"
                    :value="$element->valueResult()"
                >
                    {{ $element->valueResult() }}%
                </x-moonshine::progress-bar>
            @endif

            <div class="report-card-body">
                <div class="report-card-value">{{ $element->simpleValue() }}</div>
                <h5 class="report-card-title">{{ $element->label() }}</h5>
            </div>
        </div>
    </x-moonshine::box>
</x-moonshine::column>
