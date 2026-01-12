@if($notifications->count() > 0)
    @foreach($notifications as $notification)
        <div class="notification-item border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150 {{ $notification->unread() ? 'bg-blue-50' : '' }}" 
             data-notification-id="{{ $notification->id }}">
            <a href="{{ $notification->data['url'] ?? '#' }}" 
               class="block px-4 py-3 {{ $notification->unread() ? 'font-medium' : 'text-gray-600' }}"
               onclick="markAsRead(event, '{{ $notification->id }}')">
                <div class="flex items-start">
                    <div class="flex-shrink-0 pt-0.5">
                        <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} text-{{ $notification->unread() ? 'blue' : 'gray' }}-500"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm leading-5">
                            {{ $notification->data['message'] ?? 'New notification' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @if($notification->unread())
                        <div class="ml-2">
                            <span class="h-2 w-2 rounded-full bg-blue-500 block"></span>
                        </div>
                    @endif
                </div>
            </a>
        </div>
    @endforeach
@else
    <div class="p-4 text-center text-gray-500 text-sm">
        No notifications to display
    </div>
@endif
