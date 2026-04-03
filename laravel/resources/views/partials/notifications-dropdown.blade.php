@php
/**
 * Notifications bell dropdown partial.
 *
 * Include this in the layout header (e.g. inside nav-controls).
 * Requires an authenticated $user to display.
 *
 * Uses the NotificationService to load the initial unread count,
 * then refreshes via the /api/notifications endpoint with JS.
 */

use App\Helpers\View;
use App\Services\NotificationService;

if (!isset($user) || !$user) {
    return;
}

$notifUnreadCount = NotificationService::unreadCount($user['id']);
$notifRecent      = NotificationService::forUser($user['id'], 8);
@endphp

<style>
    .notif-wrapper { position: relative; display: inline-flex; align-items: center; }
    .notif-bell { background: none; border: none; cursor: pointer; padding: 6px; font-size: 1.3rem; color: var(--text-primary); position: relative; transition: color 0.2s; line-height: 1; }
    .notif-bell:hover { color: var(--primary); }
    .notif-badge { position: absolute; top: 0; right: 0; min-width: 17px; height: 17px; line-height: 17px; text-align: center; background: #ea4335; color: #fff; font-size: 0.65rem; font-weight: 700; border-radius: 50%; padding: 0 4px; transform: translate(40%, -30%); pointer-events: none; }
    .notif-badge.hidden { display: none; }
    .notif-dropdown { position: absolute; top: calc(100% + 8px); right: 0; width: 360px; max-height: 440px; background: var(--bg-card); border-radius: var(--radius); box-shadow: 0 8px 32px rgba(0,0,0,0.15); z-index: 1000; display: none; flex-direction: column; overflow: hidden; border: 1px solid var(--border-color); }
    .notif-dropdown.open { display: flex; }
    .notif-dropdown-header { display: flex; justify-content: space-between; align-items: center; padding: 0.8rem 1rem; border-bottom: 1px solid var(--border-color); }
    .notif-dropdown-header h4 { margin: 0; font-size: 0.95rem; color: var(--text-heading); font-weight: 700; }
    .notif-mark-all { background: none; border: none; color: var(--primary); font-size: 0.8rem; font-weight: 600; cursor: pointer; padding: 0; }
    .notif-mark-all:hover { text-decoration: underline; }
    .notif-list { flex: 1; overflow-y: auto; max-height: 340px; }
    .notif-item { display: flex; gap: 0.8rem; padding: 0.8rem 1rem; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.15s; text-decoration: none; color: inherit; }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: rgba(255,107,53,0.04); }
    .notif-item.unread { background: rgba(66,133,244,0.05); }
    .notif-item.unread:hover { background: rgba(66,133,244,0.1); }
    .notif-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .notif-icon.booking { background: rgba(255,107,53,0.12); color: #FF6B35; }
    .notif-icon.payment { background: rgba(52,168,83,0.12); color: #34a853; }
    .notif-icon.system { background: rgba(66,133,244,0.12); color: #4285f4; }
    .notif-icon.review { background: rgba(255,152,0,0.12); color: #ff9800; }
    .notif-icon.wishlist { background: rgba(156,39,176,0.12); color: #9c27b0; }
    .notif-content { flex: 1; min-width: 0; }
    .notif-content .notif-title { font-size: 0.85rem; font-weight: 600; color: var(--text-heading); margin-bottom: 0.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .notif-content .notif-msg { font-size: 0.8rem; color: var(--text-secondary); line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .notif-content .notif-time { font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.2rem; opacity: 0.7; }
    .notif-unread-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); flex-shrink: 0; align-self: center; }
    .notif-empty { padding: 2.5rem 1rem; text-align: center; color: var(--text-secondary); }
    .notif-empty .notif-empty-icon { font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.3; }
    .notif-empty p { font-size: 0.85rem; margin: 0; }
    .notif-dropdown-footer { padding: 0.6rem 1rem; border-top: 1px solid var(--border-color); text-align: center; }
    .notif-dropdown-footer a { font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 600; }
    .notif-dropdown-footer a:hover { text-decoration: underline; }

    @media (max-width: 480px) {
        .notif-dropdown { width: calc(100vw - 2rem); right: -60px; }
    }
</style>

<div class="notif-wrapper" id="notifWrapper">
    <button class="notif-bell" id="notifBell" aria-label="Notifications" title="Notifications">
        &#128276;
        <span class="notif-badge {!! $notifUnreadCount < 1 ? 'hidden' : '' !!}" id="notifBadge">{!! $notifUnreadCount !!}</span>
    </button>

    <div class="notif-dropdown" id="notifDropdown" role="menu">
        <div class="notif-dropdown-header">
            <h4>Notifications</h4>
            <button class="notif-mark-all" id="notifMarkAll" {!! $notifUnreadCount < 1 ? 'style="display:none"' : '' !!}>Mark all read</button>
        </div>

        <div class="notif-list" id="notifList">
            @if(!empty($notifRecent))
                @foreach($notifRecent as $n)
                    @php
                        $nType  = $n['type'] ?? 'system';
                        $isRead = !empty($n['is_read']);
                        $nIcon  = match ($nType) {
                            'booking'  => '&#128203;',
                            'payment'  => '&#128179;',
                            'review'   => '&#9733;',
                            'wishlist' => '&#10084;',
                            default    => '&#128276;',
                        };
                        $timeAgo = '';
                        if (!empty($n['created_at'])) {
                            $diff = time() - strtotime($n['created_at']);
                            if ($diff < 60) $timeAgo = 'just now';
                            elseif ($diff < 3600) $timeAgo = intval($diff / 60) . 'm ago';
                            elseif ($diff < 86400) $timeAgo = intval($diff / 3600) . 'h ago';
                            elseif ($diff < 604800) $timeAgo = intval($diff / 86400) . 'd ago';
                            else $timeAgo = date('M j', strtotime($n['created_at']));
                        }
                    @endphp
                    <a href="{{ $n['link'] ?? '#' }}"
                       class="notif-item {!! $isRead ? '' : 'unread' !!}"
                       data-notif-id="{!! (int) $n['id'] !!}"
                       data-read="{!! $isRead ? '1' : '0' !!}">
                        <div class="notif-icon {{ $nType }}">{!! $nIcon !!}</div>
                        <div class="notif-content">
                            <div class="notif-title">{{ $n['title'] ?? '' }}</div>
                            <div class="notif-msg">{{ $n['message'] ?? '' }}</div>
                            <div class="notif-time">{{ $timeAgo }}</div>
                        </div>
                        @if(!$isRead)
                            <div class="notif-unread-dot"></div>
                        @endif
                    </a>
                @endforeach
            @else
                <div class="notif-empty">
                    <div class="notif-empty-icon">&#128276;</div>
                    <p>No notifications yet</p>
                </div>
            @endif
        </div>

        <div class="notif-dropdown-footer">
            <a href="/account">View all activity</a>
        </div>
    </div>
</div>

<script>
(function() {
    var bell     = document.getElementById('notifBell');
    var dropdown = document.getElementById('notifDropdown');
    var badge    = document.getElementById('notifBadge');
    var markAll  = document.getElementById('notifMarkAll');
    var wrapper  = document.getElementById('notifWrapper');

    if (!bell || !dropdown) return;

    // Toggle dropdown
    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdown.classList.remove('open');
        }
    });

    // Mark single notification as read when clicked
    document.querySelectorAll('.notif-item[data-read="0"]').forEach(function(item) {
        item.addEventListener('click', function() {
            var id = this.getAttribute('data-notif-id');
            if (!id) return;

            fetch('/api/notifications/' + id + '/read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    item.classList.remove('unread');
                    item.setAttribute('data-read', '1');
                    var dot = item.querySelector('.notif-unread-dot');
                    if (dot) dot.remove();
                    updateBadge(data.unread_count);
                }
            })
            .catch(function() {});
        });
    });

    // Mark all as read
    if (markAll) {
        markAll.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    document.querySelectorAll('.notif-item.unread').forEach(function(item) {
                        item.classList.remove('unread');
                        item.setAttribute('data-read', '1');
                        var dot = item.querySelector('.notif-unread-dot');
                        if (dot) dot.remove();
                    });
                    updateBadge(0);
                    markAll.style.display = 'none';
                }
            })
            .catch(function() {});
        });
    }

    function updateBadge(count) {
        if (!badge) return;
        count = parseInt(count) || 0;
        badge.textContent = count;
        if (count < 1) {
            badge.classList.add('hidden');
        } else {
            badge.classList.remove('hidden');
        }
    }

    // Refresh notification count every 60 seconds
    setInterval(function() {
        fetch('/api/notifications?limit=1', {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                updateBadge(data.unread_count);
            }
        })
        .catch(function() {});
    }, 60000);
})();
</script>
