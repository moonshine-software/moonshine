<x-moonshine::column
    :colSpan="$element->columnSpanValue()"
    :adaptiveColSpan="$element->adaptiveColumnSpanValue()"
>
    <x-moonshine::box
        class="box-shadow zoom-in h-full p-0"
    >
        <div class="report-card">
            <div class="report-card-heading">
                {!! $element->getIcon(6, 'pink') !!}

                @if($element->isProgress())
                    <div class="report-card-indicator bg-green-500">
                        {{ $element->valueResult() }}%
                    </div>
                @endif
            </div>

            <div class="report-card-body">
                <div class="report-card-value">{{ $element->simpleValue() }}</div>
                <h5 class="report-card-title">{{ $element->label() }}</h5>
            </div>
        </div>
    </x-moonshine::box>
</x-moonshine::column>
