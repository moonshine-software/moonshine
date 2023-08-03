<div x-data="tree(@json($element->toValue()->modelKeys()))">
    {!! $element->buildTreeHtml() !!}
</div>
