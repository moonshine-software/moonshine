<x-moonshine::form.textarea
    :attributes="$element->attributes()->merge([
        'id' => 'tinyeditor_' . $item->getKey() . '_' . $element->id(),
        'name' => $element->name()
    ])"
>
    {!! $element->formViewValue($item) ?? '' !!}
</x-moonshine::form.textarea>

<script>
    var editor_config = {
        path_absolute: "/",
        selector: 'textarea#tinyeditor_{{ $item->getKey() }}_{{ $element->id() }}',
        relative_urls: false,
        plugins: '{{ trim($element->plugins . ' ' . $element->addedPlugins) }}',
        toolbar: '{{ trim($element->toolbar . ' ' . $element->addedToolbar) }}',
        @if(!empty($element->commentAuthor))
            tinycomments_mode: 'embedded',
            tinycomments_author: '{{ $element->commentAuthor }}',
        @endif
        @if(!empty($element->mergeTags))
            mergetags_list: @json($element->mergeTags),
        @endif

        @if(config('moonshine.tinymce.file_manager', false))
            file_picker_callback: function (callback, value, meta) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + '{{ config('moonshine.tinymce.file_manager', 'laravel-filemanager') }}?editor=' + meta.fieldname;
                if (meta.filetype == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                } else {
                    cmsURL = cmsURL + "&type=Files";
                }

                tinyMCE.activeEditor.windowManager.openUrl({
                    url: cmsURL,
                    title: 'Filemanager',
                    width: x * 0.8,
                    height: y * 0.8,
                    resizable: "yes",
                    close_previous: "no",
                    onMessage: (api, message) => {
                        callback(message.content);
                    }
                });
            }
        @endif
    };

    tinymce.init(editor_config);
</script>
