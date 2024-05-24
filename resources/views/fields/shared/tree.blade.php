<div x-data="tree(@json($element->treeKeys()))">
    {!! $element->toTreeHtml() !!}
</div>
