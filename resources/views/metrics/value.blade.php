<x-moonshine::column
    :colSpan="$item->adaptiveColumnSpanValue()"
    :adaptiveColSpan="$item->columnSpanValue()"
>
    <x-moonshine::box
        class="box-shadow zoom-in h-full p-0"
    >
        <div class="report-card">
            <div class="report-card-heading">
                {!! $item->getIcon(6, 'pink') !!}

                @if($item->isProgress())
                    <div class="report-card-indicator bg-green-500">
                        {{ $item->valueResult() }}%
                    </div>
                @endif
            </div>

            <div class="report-card-body">
                <div class="report-card-value">{{ $item->simpleValue() }}</div>
                <h5 class="report-card-title">{{ $item->label() }}</h5>
            </div>
        </div>
    </x-moonshine::box>
</x-moonshine::column>
