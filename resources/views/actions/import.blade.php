<div>
    <x-moonshine::modal>
        <x-slot name="title">{{ $action->label() }}</x-slot>

        <x-slot name="buttons">
            <form action="{{ $action->url() }}" enctype="multipart/form-data" method="POST">
                @csrf

                <input type="hidden" name="{{ $action->triggerKey }}" value="1">
                <input type="file"
                       required
                       name="{{ $action->inputName }}"
                       class="text-black">

                <button type="submit"
                        class="mt-4 inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    {{ trans('moonshine::ui.confirm') }}
                </button>
            </form>
        </x-slot>

        <x-slot name="outerHtml">
            <div x-on:click="isOpen = !isOpen">
                @include('moonshine::shared.btn', [
                    'title' => $action->label(),
                    'href' => '#',
                    'filled' => true,
                    'icon' => 'clip'
                ])
            </div>
        </x-slot>
    </x-moonshine::modal>
</div>
