@if($element->tabs()->isNotEmpty())
    <x-moonshine::tabs
        class="mb-4"
        id="tabs_{{ $element->id() }}"
        :tabs="$element->tabsWithHtml()->toArray()"
        :contents="$element->contentWithHtml($resource, $item)->toArray()"
    />
@endif
