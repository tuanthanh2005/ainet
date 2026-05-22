/**
 * ============================================================
 * AppNotify — Global Premium Toast Notification System
 * ============================================================
 * Usage:
 *   AppNotify.success('Đăng nhập thành công!', 'Chào mừng trở lại 👋')
 *   AppNotify.error('Đăng nhập thất bại', 'Sai tên đăng nhập hoặc mật khẩu.')
 *   AppNotify.warning('Cảnh báo', 'Phiên đăng nhập sắp hết hạn.')
 *   AppNotify.info('Thông báo', 'Đơn hàng của bạn đang được xử lý.')
 *   const id = AppNotify.loading('Đang xử lý...')
 *   AppNotify.dismiss(id)
 * ============================================================
 */
const AppNotify = (() => {
    const TYPES = {
        success: { icon: 'fa-circle-check',     title: 'Thành công',    duration: 4000 },
        error:   { icon: 'fa-circle-xmark',     title: 'Lỗi',           duration: 6000 },
        warning: { icon: 'fa-triangle-exclamation', title: 'Cảnh báo',  duration: 5000 },
        info:    { icon: 'fa-circle-info',       title: 'Thông báo',     duration: 4500 },
        loading: { icon: 'fa-circle-notch',      title: 'Đang xử lý...', duration: 0 },
    };

    let container = null;
    let counter = 0;

    function getContainer() {
        if (!container) {
            container = document.getElementById('app-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'app-toast-container';
                document.body.appendChild(container);
            }
        }
        return container;
    }

    function show(type, message, title, duration) {
        const cfg = TYPES[type] || TYPES.info;
        const id = 'toast-' + (++counter);
        const dur = (duration !== undefined && duration !== null) ? duration : cfg.duration;
        const finalTitle = title || cfg.title;

        const toast = document.createElement('div');
        toast.id = id;
        toast.className = `app-toast toast-${type}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

        toast.innerHTML = `
            <div class="app-toast-icon">
                <i class="fa-solid ${cfg.icon}"></i>
            </div>
            <div class="app-toast-body">
                <div class="app-toast-title">${finalTitle}</div>
                ${message ? `<div class="app-toast-message">${message}</div>` : ''}
            </div>
            <button class="app-toast-close" aria-label="Đóng thông báo">
                <i class="fa-solid fa-xmark"></i>
            </button>
            ${dur > 0 ? `<div class="app-toast-progress" style="transition-duration:${dur}ms"></div>` : ''}
        `;

        // Close button
        toast.querySelector('.app-toast-close').addEventListener('click', (e) => {
            e.stopPropagation();
            dismiss(id);
        });

        // Click entire toast to dismiss
        toast.addEventListener('click', () => dismiss(id));

        getContainer().appendChild(toast);

        // Animate progress bar
        if (dur > 0) {
            const bar = toast.querySelector('.app-toast-progress');
            if (bar) {
                // Force reflow to start animation
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        bar.style.transform = 'scaleX(0)';
                    });
                });
                setTimeout(() => dismiss(id), dur);
            }
        }

        return id;
    }

    function dismiss(id) {
        const toast = document.getElementById(id);
        if (!toast || toast.classList.contains('toast-hiding')) return;
        toast.classList.add('toast-hiding');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
        // Fallback remove
        setTimeout(() => toast.remove(), 500);
    }

    function dismissAll() {
        document.querySelectorAll('.app-toast').forEach(t => {
            if (!t.classList.contains('toast-hiding')) {
                t.classList.add('toast-hiding');
                setTimeout(() => t.remove(), 400);
            }
        });
    }

    return {
        success: (message, title, duration) => show('success', message, title, duration),
        error:   (message, title, duration) => show('error',   message, title, duration),
        warning: (message, title, duration) => show('warning', message, title, duration),
        info:    (message, title, duration) => show('info',    message, title, duration),
        loading: (message, title)           => show('loading', message, title, 0),
        dismiss: (id)                       => dismiss(id),
        dismissAll,
    };
})();

// Helper: copy text to clipboard and show AppNotify toast
function copyText(textOrId) {
    let text = textOrId;
    // If it is a selector or element ID, try to read the inner text
    const el = document.getElementById(textOrId);
    if (el) {
        text = el.innerText || el.textContent;
    }
    
    navigator.clipboard.writeText(text).then(() => {
        AppNotify.success('Đã sao chép: ' + text, 'Sao chép', 1800);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        AppNotify.error('Không thể sao chép vào clipboard.', 'Lỗi');
    });
}

// Helper: Toggle password field visibility
function togglePass() {
    const p = document.getElementById('u_pass');
    if (p) {
        p.type = p.type === 'password' ? 'text' : 'password';
    }
}

// Scroll to Top Logic
document.addEventListener('DOMContentLoaded', function() {
    const scrollToTopBtn = document.getElementById('btnScrollToTop');
    
    if (scrollToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        scrollToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    setupAuthRequiredActions();
});

// Product detail variant selection (visual updates only)
function selectOption(element) {
    document.querySelectorAll('.option-item').forEach(item => {
        item.classList.remove('selected');
    });
    element.classList.add('selected');

    // Also check the nested radio input
    const radio = element.querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
    }

    const price    = parseFloat(element.dataset.price) || 0;
    const original = parseFloat(element.dataset.originalPrice) || 0;
    const stock    = element.dataset.stock !== undefined ? parseInt(element.dataset.stock, 10) : null;

    const fmt = new Intl.NumberFormat('vi-VN');

    const cur  = document.getElementById('detail-current-price');
    const orig = document.getElementById('detail-original-price');
    const badge = document.getElementById('detail-discount-badge');
    const pct   = document.getElementById('detail-discount-pct');

    if (cur) cur.innerText = fmt.format(price) + 'đ';

    if (orig && badge && pct) {
        if (original > price && price > 0) {
            const off = Math.round((1 - price / original) * 100);
            orig.innerText = fmt.format(original) + 'đ';
            orig.style.display = '';
            pct.innerText = off;
            badge.style.display = '';
        } else {
            orig.style.display = 'none';
            badge.style.display = 'none';
        }
    }

    if (stock !== null && stock !== undefined) {
        const stockEl = document.getElementById('detail-stock');
        if (stockEl) stockEl.innerText = stock;
    }
}

function openLoginPrompt(message) {
    const text = message || 'Bạn cần đăng nhập để tiếp tục.';
    AppNotify.info(text, 'Yêu cầu đăng nhập');

    const loginModalEl = document.getElementById('loginModal');
    if (loginModalEl && typeof bootstrap !== 'undefined') {
        const loginModal = bootstrap.Modal.getOrCreateInstance(loginModalEl);
        loginModal.show();
    }
}

function setupAuthRequiredActions() {
    if (window.APP_USER_LOGGED_IN) {
        return;
    }

    document.querySelectorAll('a[data-auth-required="true"]').forEach(anchor => {
        anchor.addEventListener('click', function(event) {
            event.preventDefault();
            openLoginPrompt('Bạn cần đăng nhập để mua sản phẩm.');
        });
    });

    document.querySelectorAll('form[data-requires-login="buy"]').forEach(form => {
        form.addEventListener('submit', function(event) {
            const actionType = form.querySelector('[name="action_type"]');
            if (actionType && actionType.value === 'buy') {
                event.preventDefault();
                openLoginPrompt('Bạn cần đăng nhập để mua ngay.');
            }
        });
    });
}

// Homepage: purchase popup notification cycle (visual only)
function initRecentPurchasePopup() {
    const popup = document.getElementById('recent-purchase-popup');
    if (!popup) return;

    if (typeof fakeOrders === 'undefined' || fakeOrders.length === 0) {
        popup.style.display = 'none';
        return;
    }

    let orderIndex = 0;
    let hideTimeout = null;

    function showNextOrder() {
        if (orderIndex >= fakeOrders.length) orderIndex = 0;
        const order = fakeOrders[orderIndex];

        const avatarBg = document.getElementById('popup-avatar-bg');
        const avatarTxt = document.getElementById('popup-avatar-text');
        const custName = document.getElementById('popup-customer-name');
        const prodName = document.getElementById('popup-product-name');
        const prodPrice = document.getElementById('popup-product-price');
        const timeEl = document.getElementById('popup-time');
        const locEl = document.getElementById('popup-location');

        if (avatarBg) avatarBg.style.backgroundColor = order.bg;
        if (avatarTxt) avatarTxt.innerText = order.initial;
        if (custName) custName.innerText = order.name;
        if (prodName) prodName.innerText = order.product;
        if (prodPrice) prodPrice.innerText = order.price;
        if (timeEl) timeEl.innerText = order.time;
        if (locEl) locEl.innerText = order.location;

        popup.classList.add('show');
        const progressBar = document.getElementById('purchase-progress-bar');
        if (progressBar) {
            progressBar.style.width = '0%';
            setTimeout(() => {
                progressBar.style.transition = 'width 5s linear';
                progressBar.style.width = '100%';
            }, 10);
        }

        hideTimeout = setTimeout(() => {
            popup.classList.remove('show');
            if (progressBar) {
                progressBar.style.transition = 'none';
                progressBar.style.width = '0%';
            }
            orderIndex++;
            setTimeout(showNextOrder, 5000);
        }, 5000);
    }

    setTimeout(showNextOrder, 3000);
}

function closePurchasePopup() {
    const popup = document.getElementById('recent-purchase-popup');
    if (popup) {
        popup.classList.remove('show');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Initialize the visual popup notification
    initRecentPurchasePopup();
});
