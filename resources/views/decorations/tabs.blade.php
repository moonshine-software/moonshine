@if($element->tabs()->isNotEmpty())
    <x-moonshine::tabs
        :attributes="$element->attributes()->class(['mb-4'])"
        id="tabs_{{ $element->id() }}"
        :tabs="$element->tabsWithHtml()->toArray()"
        :contents="$element->contentWithHtml()->toArray()"
    />
@endif
