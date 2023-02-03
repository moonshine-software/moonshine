<div class="w-full">
    {!! $resource->extensions('tabs', $item) !!}

    @include('moonshine::base.form.shared.errors', ['errors' => $errors])

    <form x-data="editForm()"
          action="{{ $resource->route(($item->exists ? 'update' : 'store'), $item->getKey()) }}"
          class="mb-4"
          method="POST"
          @if($resource->isPrecognition())
              x-on:submit.prevent="precognition($event.target)"
          @endif
          enctype="multipart/form-data"
    >
        @if(request('relatable_mode'))
            <input type="hidden" name="relatable_mode" value="1">
        @endif

        @csrf

        @if($item->exists)
            @method('PUT')
        @endif

        <x-moonshine::resource-renderable
            :components="$resource->formComponents()"
            :item="$item"
            :resource="$resource"
        />

        <div class="py-10">
            <div class="flex items-center justify-start space-x-2">
                @include('moonshine::base.form.shared.btn', [
                    'type' => 'submit',
                    'class' => 'form_submit_button',
                    'name' => trans('moonshine::ui.save')
                ])

                @if($item->exists && !request('relatable_mode'))
                    @foreach($resource->formActions() as $index => $action)
                        @if($action->isSee($item))
                            @include('moonshine::shared.btn', [
                                'href' => $resource->route("form-action", $item->getKey(), ['index' => $index]),
                                'filled' => false,
                                'title' => $action->label(),
                                'icon' => $action->iconValue()
                            ])
                        @endif
                    @endforeach
                @endif
            </div>

            <div class="precognition_errors mt-4"></div>
        </div>
    </form>

    @if($item->exists)
        @foreach($resource->relatableFormComponents() as $field)
            @if($field->canDisplayOnForm($item))
                <div class="mt-4"></div>
                <h2 class="text-lg">{{ $field->label() }}</h2>
                <div class="mt-4"></div>
                {{ $resource->renderField($field, $item) }}
            @endif
        @endforeach
    @endif

    @if(!empty($resource->components()))
        @foreach($resource->components() as $formComponent)
            @if($formComponent->isSee($item))
                {{ $resource->renderFormComponent($formComponent, $item) }}
            @endif
        @endforeach
    @endif

    <script>
        function precognition(form) {
            form.querySelector('.form_submit_button').setAttribute('disabled', 'true');
            form.querySelector('.form_submit_button').innerHTML = '{{ trans('moonshine::ui.loading') }}';
            form.querySelector('.precognition_errors').innerHTML = '';

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'Precognition': 'true',
                },
                body: new FormData(form)
            }).then(function (response) {
                if (response.status === 200) {
                    form.submit()
                }

                return response.json();
            }).then(function (json) {
                if(Object.keys(json).length) {
                    form.querySelector('.form_submit_button').innerHTML = '{{ trans('moonshine::ui.saved_error') }}';
                    form.querySelector('.form_submit_button').removeAttribute('disabled');

                    let errors  = '';

                    for(const key in json) {
                        errors = errors + '<div class="mt-2 text-pink">' + json[key] + '</div>';
                    }

                    form.querySelector('.precognition_errors').innerHTML = errors;
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
