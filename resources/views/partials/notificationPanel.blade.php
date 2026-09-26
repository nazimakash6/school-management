<div class="notification-panel" id="notificationPanel">
    <div class="notification-header">
        <h5 class="notification-title">Notifications</h5>
        <div class="notification-actions">
            <button class="btn btn-ghost btn-sm" id="markAllRead">Mark all read</button>
            <button class="nav-btn" id="notificationClose"><i data-lucide="x"
                    style="width: 1.25rem; height: 1.25rem;"></i></button>
        </div>
    </div>
    <div class="notification-list">
        <div class="notification-item unread">
            <div class="notification-icon"
                style="background-color: var(--color-primary-100); color: var(--color-primary-700);"><i
                    data-lucide="user-plus" style="width: 1.125rem; height: 1.125rem;"></i></div>
            <div class="notification-content">
                <p class="notification-text"><strong>New admission</strong> submitted by Ahmed Khan for Class 5</p><span
                    class="notification-time">5 minutes ago</span>
            </div>
        </div>
        <div class="notification-item unread">
            <div class="notification-icon"
                style="background-color: var(--color-success-100); color: var(--color-emerald-700);"><i
                    data-lucide="wallet" style="width: 1.125rem; height: 1.125rem;"></i></div>
            <div class="notification-content">
                <p class="notification-text"><strong>Fee received</strong> Rs. 15,000 from Fatima Ali</p><span
                    class="notification-time">20 minutes ago</span>
            </div>
        </div>
        <div class="notification-item unread">
            <div class="notification-icon"
                style="background-color: var(--color-warning-50); color: var(--color-warning-600);"><i
                    data-lucide="alert-triangle" style="width: 1.125rem; height: 1.125rem;"></i></div>
            <div class="notification-content">
                <p class="notification-text"><strong>Low attendance</strong> alert for Class 8-A today</p><span
                    class="notification-time">1 hour ago</span>
            </div>
        </div>
    </div>
    <div class="notification-footer"><a href="notifications.html" class="btn btn-ghost btn-sm w-100">View all
            notifications</a></div>
</div>
<div class="notification-overlay" id="notificationOverlay"></div>