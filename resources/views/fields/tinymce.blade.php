<div class="tinymce">
    <x-moonshine::form.textarea
        :attributes="$element->attributes()->merge([
            'id' => 'tinyeditor_' . $item->getKey() . '_' . $element->id(),
            'name' => $element->name()
        ])"
        x-data="tinymce('textarea#tinyeditor_{{$item->getKey()}}_{{$element->id()}}')"
        :data-language="!empty($element->locale) ? $element->locale : app()->getLocale()"
        :data-plugins="trim($element->plugins . ' ' . $element->addedPlugins)"
        :data-toolbar="trim($element->toolbar . ' ' . $element->addedToolbar)"
        :data-tinycomments_mode="!empty($element->commentAuthor) ? 'embedded' : null"
        :data-tinycomments_author="!empty($element->commentAuthor) ? $element->commentAuthor : null"
        :data-mergetags_list="!empty($element->mergeTags) ? json_encode($element->mergeTags) : null"
        :data-file_manager="config('moonshine.tinymce.file_manager', false) ? config('moonshine.tinymce.file_manager', 'laravel-filemanager') : null"
    >{!! $element->formViewValue($item) ?? '' !!}</x-moonshine::form.textarea>
</div>
