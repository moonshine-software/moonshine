<div class="w-full">
    {!! $resource->extensions('tabs', $item) !!}

    @include('moonshine::base.form.shared.errors', ['errors' => $errors])

    <form x-data="editForm()"
          action="{{ $resource->route(($item->exists ? 'update' : 'store'), $item->getKey()) }}"
          class="bg-white dark:bg-darkblue shadow-md rounded-lg mb-4 text-white"
          method="POST"
          x-on:submit.prevent="precognition($event.target)"
          enctype="multipart/form-data"
    >
        @if(request('relatable_mode'))
            <input type="hidden" name="relatable_mode" value="1">
        @endif

        @csrf

        @if($item->exists)
            @method('PUT')
        @endif

        <div x-data="{activeTab: '{{ $resource->tabs()->first()?->id() }}'}">
            @if($resource->tabs()->isNotEmpty())
                <div>
                    <nav class="flex flex-col sm:flex-row">
                        @foreach($resource->tabs() as $tab)
                            <button
                                :class="{ 'border-b-2 font-medium border-purple': activeTab === '{{ $tab->id() }}' }"
                                @click.prevent="activeTab = '{{ $tab->id() }}'"
                                class="py-4 px-6 block focus:outline-none text-purple">
                                {{ $tab->label() }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            @endif

            @foreach($resource->formComponents() as $field)
                @if($field instanceof \Leeto\MoonShine\Decorations\Decoration)
                    {{ $resource->renderDecoration($field, $item) }}
                @elseif($field instanceof \Leeto\MoonShine\Fields\Field && $field->canDisplayFormPrimitiveField($item))
                    <x-moonshine::field-container :field="$field" :item="$item" :resource="$resource">
                        {{ $resource->renderField($field, $item) }}
                    </x-moonshine::field-container>
                @endif
            @endforeach
        </div>


        <div class="px-10 py-10">
            @include('moonshine::base.form.shared.btn', [
                'type' => 'submit',
                'class' => 'form_submit_button',
                'name' => trans('moonshine::ui.save')
            ])

            <div class="precognition_errors mt-4"></div>
        </div>
    </form>

    @if($item->exists)
        @foreach($resource->formComponents() as $field)
            @if($field instanceof \Leeto\MoonShine\Decorations\Decoration)
                {{ $resource->renderDecoration($field, $item) }}
            @elseif($field instanceof \Leeto\MoonShine\Fields\Field && $field->canDisplayFormRelationField($item))
                <div class="mt-4"></div>
                <h2 class="text-lg">{{ $field->label() }}</h2>
                <div class="mt-4"></div>
                {{ $resource->renderField($field, $item) }}
            @endif
        @endforeach
    @endif

    <script>
        function precognition(form) {
            form.querySelector('.form_submit_button').innerHTML = '{{ trans('moonshine::ui.loading') }}';
            form.querySelector('.precognition_errors').innerHTML = '';

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'Precognition': 'true',
                },
                body: new URLSearchParams(new FormData(form))
            }).then(function (response) {
                return response.json();
            }).then(function (json) {
                if(Object.keys(json).length) {
                    form.querySelector('.form_submit_button').innerHTML = '{{ trans('moonshine::ui.saved_error') }}';
                    let errors  = '';

                    for(const key in json) {
                        errors = errors + '<div class="mt-2 text-pink">' + json[key] + '</div>';
                    }

                    form.querySelector('.precognition_errors').innerHTML = errors;
                } else {
                    form.submit()
                }
            })


            return false;
        }

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
