<div class="w-full">
    {!! $resource->extensions('tabs', $item) !!}

    @include('moonshine::base.form.shared.errors', ['errors' => $errors])

    <form x-data="editForm()"
          action="{{ $resource->route(($item->exists ? 'update' : 'store'), $item->id) }}"
          class="bg-white shadow-md rounded mb-4"
          method="POST"
          enctype="multipart/form-data"
    >

        @csrf

        @if($item->exists)
            @method('PUT')
        @endif

        <div x-data="{activeTab: '{{ $resource->tabs()->first()?->id() }}'}">
            @if($resource->tabs()->isNotEmpty())
                <div>
                    <nav class="flex flex-col sm:flex-row">
                        @foreach($resource->tabs() as $tab)
                            <button :class="{ 'border-b-2 font-medium border-purple': activeTab === '{{ $tab->id() }}' }"
                                    @click.prevent="activeTab = '{{ $tab->id() }}'"
                                    class="text-gray-600 py-4 px-6 block hover:text-purple focus:outline-none text-purple">
                                {{ $tab->label() }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            @endif

            @foreach($resource->fields() as $field)
                @if($field instanceof \Leeto\MoonShine\Decorations\BaseDecoration)
                    {{ $resource->renderDecoration($field, $item) }}
                @else
                    <x-moonshine::field-container :field="$field" :item="$item" :resource="$resource">
                        {{ $resource->renderField($field, $item) }}
                    </x-moonshine::field-container>
                @endif
            @endforeach
        </div>


        <div class="px-10 py-10 bg-purple">
            @include('moonshine::base.form.shared.btn', [
                'type' => 'submit',
                'class' => '',
                'name' => trans('moonshine::ui.save')
            ])
        </div>
    </form>

    <script>
        function editForm() {
            return {
                @if($resource->whenFieldNames())
                    @foreach($resource->whenFieldNames() as $name)
                        {{ $name }}: '{{ $item->{$name} }}',
                    @endforeach
                @endif
            };
        }
    </script>
</div>