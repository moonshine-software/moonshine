@if($element->tabs()->isNotEmpty())
    <!-- Tabs -->
    <div class="tabs" x-data="{activeTab_{{ $element->id() }}: '{{ $element->tabs()->first()?->id() }}'}">
        <!-- Tabs Buttons -->
        <ul class="tabs-list">
            @foreach($element->tabs() as $tab)
                <li class="tabs-item">
                    <button
                        @click.prevent="activeTab_{{ $element->id() }} = '{{ $tab->id() }}'"
                        :class="{ '_is-active': activeTab_{{ $element->id() }} === '{{ $tab->id() }}' }"
                        class="tabs-button"
                        type="button"
                    >
                        {!! $tab->getIcon(6, 'pink') !!}
                        {{ $tab->label() }}
                    </button>
                </li>
            @endforeach
        </ul>
        <!-- END: Tabs Buttons -->

        <!-- Tabs content -->
        <div class="tabs-content">
            @foreach($element->tabs() as $tab)
                <div x-show="activeTab_{{ $element->id() }} === '{{ $tab->id() }}'" class="tab-panel" style="display: none">
                    <div class="tabs-body">
                    <x-moonshine::resource-renderable
                        :components="$tab->getFields()"
                        :item="$item"
                        :resource="$resource"
                    />
                    </div>
                </div>
            @endforeach
        </div>
        <!-- END: Tabs content -->
    </div>
    <!-- END: Tabs -->
@endif
