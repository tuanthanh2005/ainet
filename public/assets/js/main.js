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
        let isTicking = false;
        window.addEventListener('scroll', function() {
            if (!isTicking) {
                window.requestAnimationFrame(function() {
                    if (window.scrollY > 300) {
                        scrollToTopBtn.classList.add('show');
                    } else {
                        scrollToTopBtn.classList.remove('show');
                    }
                    isTicking = false;
                });
                isTicking = true;
            }
        }, { passive: true });

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

        const available = stock > 0;
        const buyButton = document.getElementById('detail-buy-button');
        const cartButton = document.getElementById('detail-cart-button');
        if (buyButton) {
            buyButton.disabled = !available;
            buyButton.textContent = available ? 'Mua Ngay' : 'Hết hàng';
            buyButton.classList.toggle('btn-buy', available);
            buyButton.classList.toggle('btn-secondary', !available);
        }
        if (cartButton) cartButton.disabled = !available;
    }
}

function switchModal(fromModal, toModal) {
    const fromEl = typeof fromModal === 'string' ? document.querySelector(fromModal) : fromModal;
    const toEl = typeof toModal === 'string' ? document.querySelector(toModal) : toModal;

    if (!toEl || typeof bootstrap === 'undefined') return;

    window.isSwitchingAuthModal = true;

    const isLocked = !!(window.isGuestExpired && !window.APP_USER_LOGGED_IN);
    const toInstance = bootstrap.Modal.getOrCreateInstance(toEl, {
        backdrop: isLocked ? 'static' : true,
        keyboard: !isLocked
    });

    if (fromEl && fromEl.classList.contains('show')) {
        const fromInstance = bootstrap.Modal.getInstance(fromEl) || bootstrap.Modal.getOrCreateInstance(fromEl);
        const onHidden = function () {
            fromEl.removeEventListener('hidden.bs.modal', onHidden);
            toInstance.show();
            setTimeout(() => { window.isSwitchingAuthModal = false; }, 150);
        };
        fromEl.addEventListener('hidden.bs.modal', onHidden);
        fromInstance.hide();
    } else {
        toInstance.show();
        setTimeout(() => { window.isSwitchingAuthModal = false; }, 150);
    }
}
window.switchModal = switchModal;

function openLoginPrompt(message) {
    const text = message || 'Bạn cần đăng nhập để tiếp tục.';
    AppNotify.info(text, 'Yêu cầu đăng nhập');

    const loginModalEl = document.getElementById('loginModal');
    if (!loginModalEl || typeof bootstrap === 'undefined') {
        const loginUrl = window.APP_LOGIN_URL || '/login';
        setTimeout(() => {
            window.location.href = loginUrl;
        }, 600);
        return;
    }

    const openModalEl = document.querySelector('.modal.show');
    if (openModalEl && openModalEl !== loginModalEl) {
        switchModal(openModalEl, loginModalEl);
    } else {
        const loginModal = bootstrap.Modal.getOrCreateInstance(loginModalEl);
        loginModal.show();
    }
}
window.openLoginPrompt = openLoginPrompt;

function setupAuthRequiredActions() {
    // Nếu đã đăng nhập thì cho phép mua bình thường
    if (window.APP_USER_LOGGED_IN) {
        return;
    }

    // Chưa đăng nhập -> Bắt buộc đăng nhập khi bấm Mua ngay
    document.querySelectorAll('a[data-auth-required="true"]').forEach(anchor => {
        anchor.addEventListener('click', function(event) {
            event.preventDefault();
            openLoginPrompt('Bạn cần đăng nhập tài khoản để mua sản phẩm.');
        });
    });

    document.querySelectorAll('form[data-requires-login="buy"]').forEach(form => {
        form.addEventListener('submit', function(event) {
            const actionType = form.querySelector('[name="action_type"]');
            if (actionType && actionType.value === 'buy') {
                event.preventDefault();
                openLoginPrompt('Bạn cần đăng nhập tài khoản để mua ngay.');
            }
        });
    });
}

// Homepage & Public Shop: Recent Purchase Social Proof Notification Toast
let purchasePopupTimer = null;
let purchasePopupHideTimer = null;
let isPopupHovered = false;

function initRecentPurchasePopup() {
    const popup = document.getElementById('recent-purchase-popup');
    if (!popup) return;

    const orders = window.recentOrdersData || window.fakeOrders || [];
    if (!Array.isArray(orders) || orders.length === 0) {
        popup.style.display = 'none';
        return;
    }

    // Check if dismissed recently (within last 90 seconds)
    try {
        const dismissedUntil = parseInt(sessionStorage.getItem('ainet_popup_dismissed_until') || '0', 10);
        if (Date.now() < dismissedUntil) {
            return;
        }
    } catch (e) {}

    // If there is a fresh real order (< 15 mins), prioritize showing it first
    const freshIdx = orders.findIndex(o => o.is_fresh);
    let orderIndex = freshIdx !== -1 ? freshIdx : Math.floor(Math.random() * orders.length);

    const realisticTimePool = [5, 8, 11, 14, 17, 19, 23, 27, 34, 42, 51];

    function showOrder() {
        // If dismissed, abort
        try {
            const dismissedUntil = parseInt(sessionStorage.getItem('ainet_popup_dismissed_until') || '0', 10);
            if (Date.now() < dismissedUntil) {
                return;
            }
        } catch (e) {}

        if (orderIndex >= orders.length) orderIndex = 0;
        const order = orders[orderIndex];

        const avatarTxt = document.getElementById('popup-avatar-text');
        const custName = document.getElementById('popup-customer-name');
        const prodName = document.getElementById('popup-product-name');
        const timeEl = document.getElementById('popup-time');

        if (avatarTxt) avatarTxt.innerText = order.initial || 'L';
        if (custName) custName.innerText = order.name || 'L*';
        if (prodName) {
            prodName.innerText = order.product || 'Tài khoản bản quyền';
            if (order.url && order.url !== '#') {
                prodName.href = order.url;
            } else {
                prodName.href = '/san-pham';
            }
        }
        
        // If fresh real order, show its actual elapsed time. Otherwise randomize realistic time >= 5m
        if (timeEl) {
            if (order.is_fresh && order.time) {
                timeEl.innerText = order.time;
            } else {
                const randomMins = realisticTimePool[Math.floor(Math.random() * realisticTimePool.length)];
                timeEl.innerText = (order.time && order.time.includes('phút')) ? order.time : (randomMins + ' phút trước');
            }
        }

        popup.classList.add('show');

        // Auto hide after 4.5 seconds (unless hovered)
        clearTimeout(purchasePopupHideTimer);
        purchasePopupHideTimer = setTimeout(() => {
            if (!isPopupHovered) {
                hideOrder();
            }
        }, 4500);
    }

    function hideOrder() {
        popup.classList.remove('show');
        orderIndex = (orderIndex + 1) % orders.length;

        // Schedule next popup after a realistic delay (12 - 18 seconds)
        const nextDelay = 12000 + Math.floor(Math.random() * 6000);
        clearTimeout(purchasePopupTimer);
        purchasePopupTimer = setTimeout(showOrder, nextDelay);
    }

    // Hover pause behavior
    popup.addEventListener('mouseenter', () => {
        isPopupHovered = true;
        clearTimeout(purchasePopupHideTimer);
    });

    popup.addEventListener('mouseleave', () => {
        isPopupHovered = false;
        if (popup.classList.contains('show')) {
            clearTimeout(purchasePopupHideTimer);
            purchasePopupHideTimer = setTimeout(hideOrder, 2500);
        }
    });

    // Start first popup 4.5s after page load
    clearTimeout(purchasePopupTimer);
    purchasePopupTimer = setTimeout(showOrder, 4500);
}

function closePurchasePopup(event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    const popup = document.getElementById('recent-purchase-popup');
    if (popup) {
        popup.classList.remove('show');
    }
    clearTimeout(purchasePopupTimer);
    clearTimeout(purchasePopupHideTimer);

    // Pause for 90 seconds on user close
    try {
        sessionStorage.setItem('ainet_popup_dismissed_until', (Date.now() + 90000).toString());
    } catch (e) {}

    // Resume after 90 seconds
    purchasePopupTimer = setTimeout(() => {
        initRecentPurchasePopup();
    }, 90000);
}

// Global Spotlight Search & Top Selling Products
function initSpotlightSearch() {
    const searchModal = document.getElementById('searchModal');
    const searchInput = document.getElementById('spotlightSearchInput');
    const clearBtn = document.getElementById('spotlightClearBtn');
    const topSection = document.getElementById('spotlightTopSellingSection');
    const resultsSection = document.getElementById('spotlightSearchResultsSection');
    const resultsList = document.getElementById('spotlightResultsList');
    const noResults = document.getElementById('spotlightNoResults');
    const queryDisplay = document.getElementById('spotlightQueryDisplay');
    const resultCount = document.getElementById('spotlightResultCount');
    const tagChips = document.querySelectorAll('.search-tag-chip');

    if (!searchModal || !searchInput) return;

    let searchIndexData = null;
    let isLoadingIndex = false;
    let debounceTimer = null;

    function removeAccents(str) {
        if (!str) return '';
        return str.normalize('NFD')
                  .replace(/[\u0300-\u036f]/g, '')
                  .replace(/đ/g, 'd')
                  .replace(/Đ/g, 'D')
                  .toLowerCase();
    }

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function fetchIndex() {
        if (searchIndexData || isLoadingIndex) return;
        isLoadingIndex = true;
        fetch('/index.php?action=searchIndex')
            .then(res => res.json())
            .then(data => {
                searchIndexData = Array.isArray(data) ? data : [];
            })
            .catch(() => {
                searchIndexData = [];
            })
            .finally(() => {
                isLoadingIndex = false;
            });
    }

    // Auto-focus when modal opens
    searchModal.addEventListener('shown.bs.modal', () => {
        searchInput.focus();
        fetchIndex();
    });

    // Reset when modal hides
    searchModal.addEventListener('hidden.bs.modal', () => {
        searchInput.value = '';
        if (clearBtn) clearBtn.classList.add('d-none');
        if (topSection) topSection.classList.remove('d-none');
        if (resultsSection) resultsSection.classList.add('d-none');
    });

    // Clear button click
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            clearBtn.classList.add('d-none');
            if (topSection) topSection.classList.remove('d-none');
            if (resultsSection) resultsSection.classList.add('d-none');
            searchInput.focus();
        });
    }

    // Quick tag chips click
    tagChips.forEach(chip => {
        chip.addEventListener('click', () => {
            const kw = chip.getAttribute('data-keyword') || chip.innerText.trim();
            searchInput.value = kw;
            performSearch(kw);
            searchInput.focus();
        });
    });

    // Live search on typing
    searchInput.addEventListener('input', (e) => {
        const val = e.target.value.trim();
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            performSearch(val);
        }, 150);
    });

    function performSearch(query) {
        if (!query || query.length < 2) {
            if (clearBtn) clearBtn.classList.add('d-none');
            if (topSection) topSection.classList.remove('d-none');
            if (resultsSection) resultsSection.classList.add('d-none');
            return;
        }

        if (clearBtn) clearBtn.classList.remove('d-none');
        if (topSection) topSection.classList.add('d-none');
        if (resultsSection) resultsSection.classList.remove('d-none');
        if (queryDisplay) queryDisplay.textContent = query;

        if (!searchIndexData) {
            fetchIndex();
            // Temporary loading state
            if (resultsList) resultsList.innerHTML = '<div class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tìm kiếm...</div>';
            setTimeout(() => performSearch(query), 300);
            return;
        }

        const normalizedQuery = removeAccents(query);
        const words = normalizedQuery.split(/\s+/).filter(Boolean);

        const matched = searchIndexData.filter(item => {
            const itemText = removeAccents((item.title || '') + ' ' + (item.cat || '') + ' ' + (item.desc || ''));
            return words.every(w => itemText.includes(w));
        });

        if (resultCount) {
            resultCount.textContent = `${matched.length} kết quả`;
        }

        if (matched.length === 0) {
            if (resultsList) resultsList.innerHTML = '';
            if (noResults) noResults.classList.remove('d-none');
            return;
        }

        if (noResults) noResults.classList.add('d-none');
        if (!resultsList) return;

        // Render matched items
        resultsList.innerHTML = matched.slice(0, 10).map(item => {
            const img = item.image ? escapeHtml(item.image) : '/assets/images/placeholder.png';
            const title = escapeHtml(item.title || '');
            const cat = escapeHtml(item.cat || 'Sản phẩm');
            const price = Number(item.price || 0) > 0 
                ? Number(item.price).toLocaleString('vi-VN') + 'đ' 
                : 'Liên hệ';
            const url = escapeHtml(item.url || '#');

            // Simple highlight for title
            let highlightedTitle = title;
            try {
                const regex = new RegExp(`(${words.join('|')})`, 'gi');
                highlightedTitle = title.replace(regex, '<mark>$1</mark>');
            } catch (e) {}

            return `
                <a href="${url}" class="spotlight-live-item">
                    <img src="${img}" alt="${title}" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 48px; height: 48px; background:#f3f4f6;" loading="lazy">
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-bold text-dark text-truncate small mb-0.5">${highlightedTitle}</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">${cat}</span>
                            <span class="fw-bold text-primary small">${price}</span>
                        </div>
                    </div>
                    <div class="text-muted flex-shrink-0">
                        <i class="fa-solid fa-arrow-up-right-from-square small"></i>
                    </div>
                </a>
            `;
        }).join('');
    }

    // Keyboard Shortcuts: Ctrl+K or Cmd+K or "/"
    document.addEventListener('keydown', (e) => {
        // Ctrl+K or Cmd+K
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const modalInstance = bootstrap.Modal.getOrCreateInstance(searchModal);
            modalInstance.show();
            return;
        }

        // "/" shortcut when not in input
        if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            const modalInstance = bootstrap.Modal.getOrCreateInstance(searchModal);
            modalInstance.show();
        }
    });
}

window.handleSpotlightSubmit = function(event) {
    const input = document.getElementById('spotlightSearchInput');
    if (!input || !input.value.trim()) {
        if (event) event.preventDefault();
        return false;
    }
    return true;
};

document.addEventListener('DOMContentLoaded', () => {
    // Initialize the visual popup notification
    initRecentPurchasePopup();
    // Initialize Spotlight Quick Search
    initSpotlightSearch();
});

