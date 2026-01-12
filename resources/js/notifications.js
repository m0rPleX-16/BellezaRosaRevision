document.addEventListener('DOMContentLoaded', function() {
    const notificationToggle = document.getElementById('notificationToggle');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const markAllReadForm = document.getElementById('markAllReadForm');
    let notificationCheckInterval;

    // Toggle notification dropdown
    if (notificationToggle && notificationDropdown) {
        notificationToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
            
            // If opening the dropdown, refresh the notifications
            if (!notificationDropdown.classList.contains('hidden')) {
                refreshNotifications();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && !notificationToggle.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });
    }

    // Handle mark all as read
    if (markAllReadForm) {
        markAllReadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
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
                    // Update the notification badge
                    updateNotificationBadge(0);
                    
                    // Update the notification list
                    refreshNotifications();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        });
    }

    // Function to refresh notifications
    function refreshNotifications() {
        fetch('/notifications/latest', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                document.getElementById('notificationList').innerHTML = data.html;
            }
            updateNotificationBadge(data.unread_count);
        })
        .catch(error => console.error('Error refreshing notifications:', error));
    }

    // Function to update the notification badge
    function updateNotificationBadge(count) {
        let badge = document.getElementById('notificationBadge');
        
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.id = 'notificationBadge';
                badge.className = 'absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center';
                notificationToggle.appendChild(badge);
            }
            badge.textContent = count > 9 ? '9+' : count;
            badge.classList.remove('hidden');
        } else if (badge) {
            badge.remove();
        }
    }

    // Check for new notifications every 30 seconds
    function startNotificationCheck() {
        // Clear any existing interval
        if (notificationCheckInterval) {
            clearInterval(notificationCheckInterval);
        }
        
        // Set up new interval
        notificationCheckInterval = setInterval(() => {
            // Only check if the dropdown is closed to avoid unnecessary requests
            if (notificationDropdown && notificationDropdown.classList.contains('hidden')) {
                fetch('/notifications/unread-count', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.unread_count);
                })
                .catch(error => console.error('Error checking unread count:', error));
            }
        }, 30000); // 30 seconds
    }

    // Start checking for notifications
    startNotificationCheck();
});

// Make these functions available globally for use in other scripts
window.markAsRead = function(event, notificationId) {
    event.preventDefault();
    
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
            if (window.updateNotificationBadge) {
                window.updateNotificationBadge(data.unread_count);
            }
            
            // If this was a link, navigate to it after marking as read
            if (event.target.href && event.target.href !== '#') {
                window.location.href = event.target.href;
            }
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
};
