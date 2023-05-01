<div x-data="tree(@json($element->formViewValue($item)->modelKeys()))">
    {!! $element->buildTreeHtml($item) !!}
</div>
