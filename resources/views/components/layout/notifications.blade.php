@props([
    'notifications'
])
@if($notifications->isNotEmpty())
    <!-- Notifications -->
    <div {{ $attributes->merge(['class' => 'notifications']) }}>
        <x-moonshine::dropdown
            placement="bottom-end"
            :title="trans('moonshine::ui.notifications.title')"
            class="w-[264px] xs:w-80"
        >
            <x-slot:toggler class="notifications-icon">
                <span class="absolute top-0 right-1 h-2 w-2 rounded-lg bg-red-500"></span>
                <x-moonshine::icon
                    icon="heroicons.outline.bell"
                    color="gray"
                    size="6"
                />
            </x-slot:toggler>

            @foreach($notifications as $notification)
                <div class="notifications-item">
                    <a href="{{ route('moonshine.notifications.read', $notification) }}"
                       class="notifications-remove"
                       title="@lang('moonshine::ui.notifications.mark_as_read')"
                    >
                        <x-moonshine::icon icon="heroicons.x-mark" />
                    </a>

                    <div class="notifications-category badge-{{ $notification->data['color'] ?? 'green' }}">
                        <x-moonshine::icon icon="heroicons.information-circle" />
                    </div>

                    <div class="notifications-content">
                        <h5 class="notifications-title"></h5>
                        <p class="notifications-text">{{ $notification->data['message'] }}</p>

                        @if(isset($notification->data['button']['link']))
                            <div class="notifications-more">
                                <a href="{{ $notification->data['button']['link'] }}">
                                    {{ $notification->data['button']['label'] }}
                                </a>
                            </div>
                        @endif

                        <span class="notifications-time">{{ $notification->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            @endforeach

            <x-slot:footer>
                <a href="{{ route('moonshine.notifications.readAll') }}" class="notifications-read">
                    @lang('moonshine::ui.notifications.mark_as_read_all')
                </a>
            </x-slot:footer>
        </x-moonshine::dropdown>
    </div>
    <!-- END: Notifications-->
@endif
