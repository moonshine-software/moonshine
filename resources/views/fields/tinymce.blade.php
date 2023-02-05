<textarea id='tinyeditor_{{ $item->getKey() }}_{{ $field->id() }}' name="{{ $field->name() }}">
    {!! $field->formViewValue($item) ?? '' !!}
</textarea>

<script>
    var editor_config = {
        path_absolute: "/",
        selector: 'textarea#tinyeditor_{{ $item->getKey() }}_{{ $field->id() }}',
        relative_urls: false,
        plugins: '{{ trim($field->plugins . ' ' . $field->addedPlugins) }}',
        toolbar: '{{ trim($field->toolbar . ' ' . $field->addedToolbar) }}',
        @if(!empty($field->commentAuthor))
            tinycomments_mode: 'embedded',
            tinycomments_author: '{{ $field->commentAuthor }}',
        @endif
        @if(!empty($field->mergeTags))
            mergetags_list: @json($field->mergeTags),
        @endif
        file_picker_callback: function (callback, value, meta) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL = editor_config.path_absolute + 'laravel-filemanager?editor=' + meta.fieldname;
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
    };

    tinymce.init(editor_config);
</script>
