@if($element->tabs()->isNotEmpty())
    <x-moonshine::tabs
        id="tabs_{{ $element->id() }}"
        :attributes="$element->attributes()->class(['mb-4'])"
        :active="$element->getActive()"
        :tabs="$element->tabsWithHtml()->toArray()"
        :contents="$element->contentWithHtml()->toArray()"
    />
@endif
