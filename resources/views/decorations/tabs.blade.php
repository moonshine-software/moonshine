@if($element->tabs()->isNotEmpty())
    <x-moonshine::tabs
        id="tabs_{{ $element->id() }}"
        :tabs="$element->tabsWithHtml()->toArray()"
        :contents="$element->contentWithHtml($resource, $item)->toArray()"
    />
@endif
