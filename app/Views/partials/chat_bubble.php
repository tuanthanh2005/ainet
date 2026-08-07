<!-- Chat Box Window -->
<div id="chat-window" class="chat-window-hidden">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-avatar">
                    <i class="fa-solid fa-headset"></i>
                    <span class="online-indicator"></span>
                </div>
                <div>
                    <h6 class="chat-title">Hỗ Trợ Trực Tuyến</h6>
                    <span class="chat-subtitle"><i class="fa-solid fa-circle text-success me-1" style="font-size:0.5rem;"></i>Admin sẵn sàng hỗ trợ</span>
                </div>
            </div>
            <button id="chat-window-close" type="button" aria-label="Đóng chat">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Body: Messages List -->
        <div class="chat-body" id="chat-messages-container">
            <div class="chat-welcome-notice">
                <div class="notice-icon"><i class="fa-solid fa-sparkles"></i></div>
                <p class="mb-1"><strong>Chào mừng bạn đến với <?php echo htmlspecialchars(SITENAME); ?>!</strong></p>
                <p class="small text-muted mb-2">Gửi mã đơn của bạn vào đây để Admin check nhanh nhất nhé!</p>
                <div class="quick-replies-tags">
                    <button type="button" class="quick-tag" onclick="ChatWidget.sendQuickMessage('Cần hỗ trợ bảo hành')">🛡️ Cần hỗ trợ bảo hành</button>
                    <button type="button" class="quick-tag" onclick="ChatWidget.fillOrderCodePrompt()">📦 Gửi mã đơn hàng</button>
                </div>
            </div>
            <div id="chat-messages-list"></div>
        </div>

        <!-- Footer: Input Area -->
        <div class="chat-footer">
            <form id="chat-form" onsubmit="ChatWidget.handleSend(event)">
                <button type="button" id="chat-image-btn" class="btn btn-link p-0 border-0 shadow-none text-secondary me-1" onclick="document.getElementById('chat-image-input').click()" title="Gửi hình ảnh">
                    <i class="fa-regular fa-image" style="font-size: 1.15rem;"></i>
                </button>
                <input type="file" id="chat-image-input" accept="image/png,image/jpeg,image/gif,image/webp" style="display:none;" onchange="ChatWidget.handleImageUpload(event)">
                <input type="text" id="chat-input" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button type="submit" id="chat-send-btn" title="Gửi tin nhắn">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

<script>
window.ChatWidget = (function() {
    let isOpen = false;
    let pollInterval = null;
    let lastMessageCount = 0;
    const baseUrl = document.querySelector('meta[name="app-base"]')?.content || '/';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function init() {
        const toggleBtn = document.getElementById('chat-bubble-toggle');
        const closeBtn = document.getElementById('chat-window-close');
        
        if (!toggleBtn) return;

        toggleBtn.addEventListener('click', toggleChatWindow);
        if (closeBtn) closeBtn.addEventListener('click', toggleChatWindow);

        // Fetch messages immediately
        fetchMessages(false);

        // Start background polling every 3.5s
        startPolling(3500);

        // Pause/slow polling when tab is hidden to optimize CPU and bandwidth
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                startPolling(15000);
            } else {
                fetchMessages(isOpen);
                startPolling(3500);
            }
        });
    }

    function startPolling(ms) {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => {
            fetchMessages(isOpen);
        }, ms);
    }

    function toggleChatWindow() {
        const win = document.getElementById('chat-window');
        const badge = document.getElementById('chat-unread-badge');
        if (!win) return;

        isOpen = !isOpen;
        if (isOpen) {
            win.classList.remove('chat-window-hidden');
            win.classList.add('chat-window-visible');
            badge.classList.add('d-none');
            badge.textContent = '0';
            scrollToBottom();
            document.getElementById('chat-input')?.focus();
            // Mark as read on server
            fetchMessages(true);
        } else {
            win.classList.remove('chat-window-visible');
            win.classList.add('chat-window-hidden');
        }
    }

    function formatTime(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr.replace(/-/g, '/'));
        if (isNaN(d.getTime())) return '';
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function formatMsgContent(text) {
        if (!text) return '';
        const trimmed = text.trim();
        if (trimmed.startsWith('[img]') && trimmed.endsWith('[/img]')) {
            const url = trimmed.substring(5, trimmed.length - 6);
            return `<a href="${escapeHtml(url)}" target="_blank" class="d-inline-block mt-1"><img src="${escapeHtml(url)}" class="chat-msg-img" alt="Hình ảnh" style="max-width:180px; max-height:180px; border-radius:10px; border:1px solid #cbd5e1; object-fit:cover;"></a>`;
        }
        if (/^https?:\/\/.+\.(png|jpg|jpeg|gif|webp)(\?.*)?$/i.test(trimmed)) {
            return `<a href="${escapeHtml(trimmed)}" target="_blank" class="d-inline-block mt-1"><img src="${escapeHtml(trimmed)}" class="chat-msg-img" alt="Hình ảnh" style="max-width:180px; max-height:180px; border-radius:10px; border:1px solid #cbd5e1; object-fit:cover;"></a>`;
        }
        return escapeHtml(text);
    }

    let lastMessagesFingerprint = '';

    function renderMessages(messages, forceScroll = false) {
        const list = document.getElementById('chat-messages-list');
        const container = document.getElementById('chat-messages-container');
        if (!list || !container) return;

        const newFingerprint = (messages || []).map(m => `${m.id}_${m.is_read}`).join('|');
        if (newFingerprint === lastMessagesFingerprint && !forceScroll) {
            return;
        }

        const isNearBottom = (container.scrollHeight - container.scrollTop <= container.clientHeight + 80);

        let html = '';
        (messages || []).forEach(msg => {
            const isAdmin = msg.sender_type === 'admin';
            const timeStr = formatTime(msg.created_at);
            const contentHtml = formatMsgContent(msg.message);
            
            if (isAdmin) {
                html += `
                <div class="msg-group msg-admin">
                    <div class="msg-avatar"><i class="fa-solid fa-headset"></i></div>
                    <div class="msg-content-wrapper">
                        <span class="msg-sender">${escapeHtml(msg.sender_name || 'Admin')}</span>
                        <div class="msg-bubble">${contentHtml}</div>
                        <span class="msg-time">${timeStr}</span>
                    </div>
                </div>`;
            } else {
                html += `
                <div class="msg-group msg-user">
                    <div class="msg-content-wrapper">
                        <div class="msg-bubble">${contentHtml}</div>
                        <span class="msg-time">${timeStr}</span>
                    </div>
                </div>`;
            }
        });

        const isNewMsg = (messages || []).length > lastMessageCount;
        list.innerHTML = html;
        lastMessageCount = (messages || []).length;
        lastMessagesFingerprint = newFingerprint;

        if (forceScroll || isNewMsg || isNearBottom) {
            requestAnimationFrame(() => {
                container.scrollTop = container.scrollHeight;
            });
        }
    }

    function scrollToBottom() {
        const container = document.getElementById('chat-messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    let lastAdminMsgCount = -1;

    function playNotificationSound() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const now = ctx.currentTime;
            
            const osc1 = ctx.createOscillator();
            const osc2 = ctx.createOscillator();
            const gain = ctx.createGain();

            osc1.type = 'sine';
            osc2.type = 'sine';

            osc1.frequency.setValueAtTime(659.25, now);
            osc2.frequency.setValueAtTime(987.77, now + 0.08);

            gain.gain.setValueAtTime(0.15, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

            osc1.connect(gain);
            osc2.connect(gain);
            gain.connect(ctx.destination);

            osc1.start(now);
            osc1.stop(now + 0.08);

            osc2.start(now + 0.08);
            osc2.stop(now + 0.35);
        } catch (e) {}
    }

    let cooldownTimer = null;
    let isCooldownActive = false;
    let isMaxWaiting = false;

    function startCooldown(seconds = 5) {
        if (isMaxWaiting) return;
        
        isCooldownActive = true;
        let remaining = seconds;

        const input = document.getElementById('chat-input');
        const sendBtn = document.getElementById('chat-send-btn');
        const imgBtn = document.getElementById('chat-image-btn');
        const tags = document.querySelectorAll('.quick-tag');

        if (input) {
            input.disabled = true;
            input.placeholder = `Chờ (${remaining}s) để nhắn tiếp...`;
        }
        if (sendBtn) sendBtn.disabled = true;
        if (imgBtn) imgBtn.disabled = true;
        tags.forEach(t => t.disabled = true);

        if (cooldownTimer) clearInterval(cooldownTimer);

        cooldownTimer = setInterval(() => {
            remaining--;
            if (remaining > 0) {
                if (input && isCooldownActive && !isMaxWaiting) {
                    input.placeholder = `Chờ (${remaining}s) để nhắn tiếp...`;
                }
            } else {
                clearInterval(cooldownTimer);
                cooldownTimer = null;
                isCooldownActive = false;

                if (!isMaxWaiting) {
                    if (input) {
                        input.disabled = false;
                        input.placeholder = 'Nhập tin nhắn...';
                        input.focus();
                    }
                    if (sendBtn) sendBtn.disabled = false;
                    if (imgBtn) imgBtn.disabled = false;
                    tags.forEach(t => t.disabled = false);
                }
            }
        }, 1000);
    }

    function updateWaitingState(isWaiting) {
        isMaxWaiting = isWaiting;

        const input = document.getElementById('chat-input');
        const sendBtn = document.getElementById('chat-send-btn');
        const imgBtn = document.getElementById('chat-image-btn');
        const tags = document.querySelectorAll('.quick-tag');
        const noticeContainer = document.getElementById('chat-waiting-notice');

        if (isWaiting) {
            if (cooldownTimer) {
                clearInterval(cooldownTimer);
                cooldownTimer = null;
            }
            isCooldownActive = false;

            if (input) {
                input.disabled = true;
                input.placeholder = 'Đang chờ Admin phản hồi...';
            }
            if (sendBtn) sendBtn.disabled = true;
            if (imgBtn) imgBtn.disabled = true;
            tags.forEach(t => t.disabled = true);

            if (!noticeContainer) {
                const footer = document.querySelector('.chat-footer');
                if (footer) {
                    const notice = document.createElement('div');
                    notice.id = 'chat-waiting-notice';
                    notice.className = 'text-center text-muted small py-1 bg-light border-top';
                    notice.style.fontSize = '0.75rem';
                    notice.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i> Bạn đã gửi 10 tin nhắn liên tiếp. Vui lòng chờ Admin trả lời...';
                    footer.parentNode.insertBefore(notice, footer);
                }
            }
        } else if (!isCooldownActive) {
            if (input) {
                input.disabled = false;
                input.placeholder = 'Nhập tin nhắn...';
            }
            if (sendBtn) sendBtn.disabled = false;
            if (imgBtn) imgBtn.disabled = false;
            tags.forEach(t => t.disabled = false);

            if (noticeContainer) {
                noticeContainer.remove();
            }
        }
    }

    function fetchMessages(markRead = false) {
        const url = `${baseUrl}index.php?action=chatGetMessages${markRead ? '&mark_read=1' : ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const messages = data.messages || [];
                    renderMessages(messages);
                    
                    updateWaitingState(Boolean(data.is_waiting));

                    const adminMsgs = messages.filter(m => m.sender_type === 'admin');
                    if (lastAdminMsgCount >= 0 && adminMsgs.length > lastAdminMsgCount) {
                        playNotificationSound();
                    }
                    lastAdminMsgCount = adminMsgs.length;

                    // Update badge if closed and has unread admin messages
                    const badge = document.getElementById('chat-unread-badge');
                    if (badge && !isOpen) {
                        const unread = data.unread_count || 0;
                        if (unread > 0) {
                            badge.textContent = unread > 9 ? '9+' : unread;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }
                }
            })
            .catch(() => {});
    }

    function handleSend(e) {
        if (e) e.preventDefault();
        const input = document.getElementById('chat-input');
        if (!input) return;
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        const url = `${baseUrl}index.php?action=chatSendMessage`;
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderMessages(data.messages || []);
                scrollToBottom();
                if (data.is_waiting) {
                    updateWaitingState(true);
                } else {
                    startCooldown(5);
                }
            } else if (data.message) {
                if (data.waiting_admin) updateWaitingState(true);
                if (window.AppNotify) AppNotify.warning(data.message);
            }
        })
        .catch(() => {
            if (window.AppNotify) AppNotify.error('Không thể kết nối máy chủ');
        });
    }

    function sendQuickMessage(text) {
        const input = document.getElementById('chat-input');
        if (input) {
            input.value = text;
            handleSend();
        }
    }

    function fillOrderCodePrompt() {
        const input = document.getElementById('chat-input');
        if (input) {
            input.value = 'Mã đơn hàng của tôi là: ';
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }
    }

    function handleImageUpload(e) {
        const file = e.target.files?.[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);

        fetch(`${baseUrl}index.php?action=chatUploadImage`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderMessages(data.messages || []);
                scrollToBottom();
                if (data.is_waiting) {
                    updateWaitingState(true);
                } else {
                    startCooldown(5);
                }
            } else if (data.message) {
                if (data.waiting_admin) updateWaitingState(true);
                if (window.AppNotify) AppNotify.warning(data.message);
            }
        })
        .catch(() => {
            if (window.AppNotify) AppNotify.error('Không thể tải hình ảnh lên.');
        })
        .finally(() => {
            e.target.value = '';
        });
    }

    document.addEventListener('DOMContentLoaded', init);

    return {
        handleSend,
        sendQuickMessage,
        fillOrderCodePrompt,
        handleImageUpload,
        toggleChatWindow
    };
})();
</script>
