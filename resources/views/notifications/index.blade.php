@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-800">Notifications</h2>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150 {{ $notification->unread() ? 'bg-blue-50' : '' }}">
                        <a href="{{ $notification->data['url'] ?? '#' }}" 
                           class="block"
                           onclick="markAsRead(event, '{{ $notification->id }}')">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 pt-0.5">
                                    <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} text-{{ $notification->unread() ? 'blue' : 'gray' }}-500"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm leading-5 {{ $notification->unread() ? 'font-medium text-gray-900' : 'text-gray-700' }}">
                                        {{ $notification->data['message'] ?? 'New notification' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $notification->created_at->format('M j, Y g:i A') }}
                                        @if($notification->unread())
                                            <span class="ml-2 text-xs text-blue-600">Unread</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
                
                <div class="px-6 py-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                    <p class="text-lg">No notifications to display</p>
                    <p class="text-sm mt-2">When you get notifications, they'll appear here.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function markAsRead(event, notificationId) {
        event.preventDefault();
        
        // Mark as read via AJAX
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ _method: 'POST' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the notification badge count
                updateNotificationBadge(data.unread_count);
                
                // Remove unread styling
                const notificationItem = event.target.closest('.hover\:bg-blue-50');
                if (notificationItem) {
                    notificationItem.classList.remove('bg-blue-50');
                    const textElement = notificationItem.querySelector('.font-medium');
                    if (textElement) {
                        textElement.classList.remove('font-medium', 'text-gray-900');
                        textElement.classList.add('text-gray-700');
                    }
                }
                
                // If this was a link, navigate to it after marking as read
                if (event.target.href && event.target.href !== '#') {
                    window.location.href = event.target.href;
                }
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }
    
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }
</script>
@endpush
