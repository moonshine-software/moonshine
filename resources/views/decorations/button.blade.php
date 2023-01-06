<div class="px-10 py-5">
    @include('moonshine::shared.btn', [
        'href' => $decoration->getLinkValue(),
        'target' => $decoration->isLinkBlank() ? '_blank' : '_self',
        'filled' => true,
        'icon' => $decoration->iconValue(),
        'title' => $decoration->getLinkName()
    ])
</div>
