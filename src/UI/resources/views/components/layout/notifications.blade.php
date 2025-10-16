@props([
    'notifications',
    'readAllRoute' => '',
    'translates' => [],
])
@if($notifications->isNotEmpty())
    <!-- Notifications -->
    <div {{ $attributes->merge(['class' => 'notifications']) }}>
        <x-moonshine::dropdown
            placement="bottom-end"
            :title="$translates['title']"
            class="w-[264px] xs:w-80"
        >
            <x-slot:toggler class="notifications-trigger">
                <span class="notifications-trigger-dot"></span>
                <x-moonshine::icon icon="bell" />
            </x-slot:toggler>

            @foreach($notifications as $notification)
                <div class="notifications-item">
                    <a href="{{ $notification->getReadRoute() }}"
                       class="notifications-remove"
                       title="{{ $translates['mark_as_read'] }}"
                    >
                        <x-moonshine::icon icon="x-mark" />
                    </a>

                    <div class="notifications-category text-{{ $notification->getColor() }}">
                        <x-moonshine::icon :icon="$notification->getIcon()" />
                    </div>

                    <div class="notifications-content">
                        <p class="notifications-text">{{ $notification->getMessage() }}</p>

                        @if(!is_null($notification->getButton()))
                            <div class="notifications-more">
                                <a href="{{ $notification->getButton()->getLink() }}" {{ $notification->getButton()->getAttributes() }}>
                                    {{ $notification->getButton()->getLabel() }}
                                </a>
                            </div>
                        @endif

                        <span class="notifications-time">{{ $notification->getDate()->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            @endforeach

            <x-slot:footer>
                <a href="{{ $readAllRoute }}" class="notifications-read">
                    {{ $translates['mark_as_read_all'] }}
                </a>
            </x-slot:footer>
        </x-moonshine::dropdown>
    </div>
    <!-- END: Notifications-->
@endif
