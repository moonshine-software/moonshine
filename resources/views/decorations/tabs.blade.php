@if($decoration->tabs()->isNotEmpty())
    <div x-data="{activeTab_{{ $decoration->id() }}: '{{ $decoration->tabs()->first()?->id() }}'}">
        <div>
            <nav class="flex flex-col sm:flex-row">
                @foreach($decoration->tabs() as $tab)
                    <button
                        :class="{ 'border-b-2 font-medium border-purple': activeTab_{{ $decoration->id() }} === '{{ $tab->id() }}' }"
                        @click.prevent="activeTab_{{ $decoration->id() }} = '{{ $tab->id() }}'"
                        class="py-4 px-6 block focus:outline-none text-purple">
                        {{ $tab->label() }}
                    </button>
                @endforeach
            </nav>
        </div>

        @foreach($decoration->tabs() as $tab)
            <div x-show="activeTab_{{ $decoration->id() }} === '{{ $tab->id() }}'">
                <x-moonshine::resource-renderable
                    :components="$tab->fields()"
                    :item="$item"
                    :resource="$resource"
                />
            </div>
        @endforeach
    </div>
@endif
