<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AI CỦA TÔI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --pure-black: #000000;
            --dark-gray: #111111;
            --mid-gray: #666666;
            --light-gray: #f4f6f9;
            --border-color: #e5e7eb;
            --pure-white: #ffffff;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-gray);
            margin: 0;
            overflow: hidden;
            height: 100vh;
        }

        /* ================= LAYOUT ================= */
        .admin-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            background-color: var(--pure-black);
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background-color: var(--pure-black);
            color: var(--pure-white);
            display: flex;
            flex-direction: column;
            transition: 0.3s;
            z-index: 1000;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            flex-shrink: 0;
            border-right: 1px solid #1f1f23;
        }

        .sidebar-brand {
            padding: 16px 20px;
            font-size: 1.25rem;
            font-weight: 700;
            text-align: center;
            border-bottom: 1px solid #222;
            letter-spacing: -0.5px;
            flex-shrink: 0;
            background-color: var(--pure-black);
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-menu {
            list-style: none;
            padding: 10px 8px;
            margin: 0;
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-menu::-webkit-scrollbar {
            width: 4px;
        }
        .nav-menu::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 4px;
        }
        .nav-menu::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }
        .nav-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .nav-item {
            padding: 0;
            margin-bottom: 2px;
        }

        .nav-link {
            color: #a1a1aa;
            text-decoration: none;
            padding: 9px 12px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.88rem;
            white-space: nowrap;
        }
        .nav-label {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nav-count-badge {
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: #f59e0b;
            color: #111;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 800;
            line-height: 1;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.08);
            margin-left: 6px;
        }

        .nav-link i {
            width: 20px;
            font-size: 0.95rem;
            margin-right: 8px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link:hover {
            background-color: #1f1f23;
            color: var(--pure-white);
        }
        .nav-link.active {
            background-color: #27272a;
            color: var(--pure-white);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .sidebar-user-footer {
            flex-shrink: 0;
            background-color: #09090b;
            border-top: 1px solid #1f1f23 !important;
            padding: 12px 14px;
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1 1 auto;
            min-width: 0;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            background-color: var(--light-gray);
        }

        .topbar {
            background: var(--pure-white);
            height: 64px;
            min-height: 64px;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 99;
            flex-shrink: 0;
        }

        .content-area {
            padding: 24px;
            flex: 1 0 auto;
        }

        /* ================= UI ELEMENTS ================= */
        .card-custom {
            background: var(--pure-white);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .card-header-custom {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-custom th {
            background-color: #f9fafb;
            color: var(--mid-gray);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
        }

        .table-custom td {
            padding: 16px 24px;
            vertical-align: middle;
            color: var(--dark-gray);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        .img-thumbnail-custom {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        /* Buttons */
        .btn-black {
            background-color: var(--pure-black);
            color: var(--pure-white);
            border: 1px solid var(--pure-black);
            font-weight: 500;
            border-radius: 8px;
            padding: 8px 16px;
            transition: 0.2s;
        }

        .btn-black:hover {
            background-color: var(--dark-gray);
            color: var(--pure-white);
        }

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin-right: 5px;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--mid-gray);
            transition: 0.2s;
        }

        .btn-action:hover {
            background: var(--light-gray);
            color: var(--pure-black);
        }

        .btn-action.delete:hover {
            color: #ef4444;
            border-color: #ef4444;
            background: #fef2f2;
        }

        /* Forms */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 10px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--pure-black);
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--dark-gray);
        }

        /* Custom sleek pagination & toolbars */
        .pagination .page-link {
            color: var(--dark-gray);
            border-color: var(--border-color);
            background-color: var(--pure-white);
            border-radius: 6px !important;
            margin: 0 2px;
            padding: 5px 11px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--pure-black);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--pure-black);
            border-color: var(--pure-black);
            color: var(--pure-white);
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        .pagination .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: var(--border-color);
            cursor: not-allowed;
        }

        .table-empty-state {
            padding: 3rem 1rem !important;
            text-align: center !important;
            color: var(--mid-gray) !important;
        }

        .table-empty-state i {
            font-size: 2.2rem;
            color: #cbd5e1;
            margin-bottom: 0.75rem;
            display: block;
        }

        /* Hide Views */
        .view-section {
            display: none;
        }

        .view-section.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* ===== BLOG MODAL: image picker + rich editor ===== */
        .blog-image-preview-wrap {
            width: 140px;
            height: 100px;
            border: 1px dashed var(--border-color);
            border-radius: 10px;
            background: #f9fafb;
            position: relative;
            flex: 0 0 auto;
            overflow: hidden;
        }
        .blog-image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        .blog-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: var(--mid-gray);
            font-size: 0.8rem;
        }
        .blog-image-placeholder i { font-size: 1.6rem; opacity: 0.6; }

        /* Product image upload preview */
        .product-image-preview-wrap {
            width: 120px;
            height: 120px;
            border: 1px dashed var(--border-color);
            border-radius: 10px;
            background: #f9fafb;
            position: relative;
            flex: 0 0 auto;
            overflow: hidden;
        }
        .product-image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        .product-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: var(--mid-gray);
            font-size: 0.8rem;
        }
        .product-image-placeholder i { font-size: 1.6rem; opacity: 0.6; }

        .rich-toolbar { gap: 4px; }
        .rich-toolbar .btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0;
            font-weight: 600;
        }
        .rich-toolbar .btn[data-arg^="H"] { width: auto; padding: 0 8px; }
        .product-detail-toolbar .btn {
            width: auto;
            min-width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 8px;
            font-weight: 600;
        }
        .product-detail-toolbar .btn.icon-only {
            width: 32px;
            padding: 0;
        }
        .product-detail-editor:empty::before {
            content: attr(data-placeholder);
            color: #9ca3af;
        }
        .rich-editor {
            min-height: 220px;
            max-height: 360px;
            overflow-y: auto;
            padding: 12px 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.95rem;
            line-height: 1.6;
            background: #fff;
            outline: none;
        }
        .rich-editor:focus { border-color: #aaa; box-shadow: 0 0 0 0.15rem rgba(0,0,0,0.05); }
        .rich-editor h1 { font-size: 1.25rem; font-weight: 800; margin: 12px 0 6px; }
        .rich-editor h2 { font-size: 1.12rem; font-weight: 750; margin: 12px 0 6px; }
        .rich-editor h3 { font-size: 1rem; font-weight: 700; margin: 10px 0 6px; }
        .rich-editor p { margin: 0 0 10px; }
        .rich-editor table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            background: #fff;
        }
        .rich-editor th,
        .rich-editor td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            vertical-align: top;
        }
        .rich-editor th {
            background: #f8fafc;
            color: #111;
            font-weight: 700;
        }
        .rich-editor blockquote {
            border-left: 3px solid #111;
            padding: 4px 12px;
            color: #555;
            font-style: italic;
            margin: 8px 0;
        }
        .rich-editor ul, .rich-editor ol { padding-left: 22px; }
        .seo-quality-box {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: #f8fafc;
            padding: 12px 14px;
        }
        .seo-quality-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 6px;
        }
        .seo-quality-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: #6b7280;
            font-size: 0.82rem;
            line-height: 1.35;
        }
        .seo-quality-list li.ok { color: #15803d; }
        .seo-quality-list li.warn { color: #b45309; }
        .seo-quality-list i { margin-top: 2px; }

        /* ================= RESPONSIVE ADJUSTMENTS ================= */
        @media (max-width: 991.98px) {
            .admin-wrapper {
                width: 100%;
                overflow-x: hidden;
            }
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: var(--sidebar-width);
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1050;
                box-shadow: 4px 0 15px rgba(0, 0, 0, 0.25);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                width: 100%;
                overflow-x: hidden;
            }
            .topbar {
                padding: 0 15px;
            }
            .content-area {
                padding: 15px;
            }
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .sidebar-backdrop.show {
                display: block;
                opacity: 1;
            }
        }

        @media (max-width: 576px) {
            .topbar h5 {
                font-size: 1.1rem;
            }
            .card-header-custom {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 15px;
            }
            .card-header-custom > div:last-child {
                width: 100%;
                display: flex;
                justify-content: flex-start;
                gap: 8px;
            }
            .table-custom th, .table-custom td {
                padding: 10px 12px;
                font-size: 0.82rem;
            }
            .img-thumbnail-custom {
                width: 40px;
                height: 40px;
            }
            .btn-black, .btn-light {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
    <script>
        // Get saved admin tab from query param, hash, or localStorage (defaults to dashboard)
        function getSavedAdminTab() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const paramTab = urlParams.get('tab');
                if (paramTab && document.getElementById('view-' + paramTab)) return paramTab;

                const hash = (window.location.hash || '').replace(/^#/, '');
                if (hash && document.getElementById('view-' + hash)) return hash;

                const saved = localStorage.getItem('admin_active_tab');
                if (saved && document.getElementById('view-' + saved)) return saved;
            } catch (e) {}
            return 'dashboard';
        }

        // Define core layout functions early so menu click handlers work immediately
        function switchView(viewId, el, updateHistory) {
            if (!viewId) return;

            document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
            if (el) {
                el.classList.add('active');
            } else {
                const targetNav = document.querySelector(`.nav-menu a[data-view="${viewId}"]`)
                               || document.querySelector(`.nav-menu a[href="#${viewId}"]`)
                               || document.querySelector(`.nav-menu a[onclick*="'${viewId}'"]`);
                if (targetNav) targetNav.classList.add('active');
            }

            const titles = {
                'dashboard': 'Tổng quan',
                'products': 'Quản lý Sản phẩm',
                'orders': 'Quản lý Đơn hàng',
                'contacts': 'Quản lý Liên hệ',
                'categories': 'Quản lý Danh mục',
                'users': 'Quản lý User',
                'blogs': 'Quản lý Tin tức',
                'settings': 'Cấu hình Website',
                'indexing': 'Quản lý Index Google',
                'keywords': 'Quản lý Từ khóa SEO',
                'chat': 'Hộp thư hỗ trợ',
                'chats': 'Quản lý Chat Box',
                'security-logs': 'Log An Ninh & Session'
            };
            const pageTitle = document.getElementById('page-title');
            if (pageTitle) pageTitle.innerText = titles[viewId] || 'Quản trị';

            document.querySelectorAll('.view-section').forEach(view => view.classList.remove('active'));
            const targetView = document.getElementById('view-' + viewId);
            if (targetView) targetView.classList.add('active');

            try {
                localStorage.setItem('admin_active_tab', viewId);
            } catch(e) {}

            if (updateHistory !== false) {
                try {
                    if (window.location.hash !== '#' + viewId) {
                        if (window.history && window.history.replaceState) {
                            window.history.replaceState(null, '', '#' + viewId);
                        } else {
                            window.location.hash = viewId;
                        }
                    }
                } catch(e) {}
            }

            if (viewId === 'dashboard') {
                if (typeof renderDashboardChart === 'function') {
                    setTimeout(renderDashboardChart, 50);
                }
            }

            if (viewId === 'indexing') {
                if (typeof refreshIndexingCounts === 'function') refreshIndexingCounts();
            }

            if (viewId === 'security-logs') {
                if (typeof loadSecurityLogs === 'function') loadSecurityLogs(true);
                if (!window.activeSessionsInterval) {
                    if (typeof loadActiveSessionsOnly === 'function') {
                        window.activeSessionsInterval = setInterval(loadActiveSessionsOnly, 5000);
                    }
                }
                if (!window.historyLogsInterval) {
                    if (typeof loadSecurityLogs === 'function') {
                        window.historyLogsInterval = setInterval(() => loadSecurityLogs(true), 120000);
                    }
                }
            } else {
                if (window.activeSessionsInterval) {
                    clearInterval(window.activeSessionsInterval);
                    window.activeSessionsInterval = null;
                }
                if (window.historyLogsInterval) {
                    clearInterval(window.historyLogsInterval);
                    window.historyLogsInterval = null;
                }
            }

            if (window.innerWidth < 992) {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar && sidebar.classList.contains('show')) {
                    if (typeof toggleSidebar === 'function') toggleSidebar();
                }
            }
        }

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            if (!sidebar) return;
            let backdrop = document.querySelector('.sidebar-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'sidebar-backdrop';
                backdrop.addEventListener('click', toggleSidebar);
                document.body.appendChild(backdrop);
            }

            if (sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                setTimeout(() => {
                    if (!sidebar.classList.contains('show') && backdrop.parentNode) {
                        backdrop.style.display = 'none';
                    }
                }, 300);
            } else {
                backdrop.style.display = 'block';
                backdrop.offsetHeight;
                sidebar.classList.add('show');
                backdrop.classList.add('show');
            }
        }
    </script>
</head>

<body>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-brand d-flex align-items-center justify-content-between px-3">
                <span class="flex-grow-1 text-center"><i class="fa-solid fa-circle-nodes me-2"></i>AI CỦA TÔI</span>
                <button class="btn btn-close btn-close-white d-lg-none shadow-none" style="font-size: 0.8rem;" onclick="toggleSidebar()" aria-label="Close"></button>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#dashboard" data-view="dashboard" class="nav-link" onclick="switchView('dashboard', this); return false;">
                        <i class="fa-solid fa-chart-pie"></i> Tổng quan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#products" data-view="products" class="nav-link" onclick="switchView('products', this); return false;">
                        <i class="fa-solid fa-box"></i> Quản lý Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#orders" data-view="orders" class="nav-link" onclick="switchView('orders', this); return false;">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="nav-label">Quản lý Đơn hàng</span>
                        <?php if (!empty($pendingOrders)): ?>
                            <span class="nav-count-badge" title="Đơn cần xử lý"><?php echo (int) $pendingOrders; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#contacts" data-view="contacts" class="nav-link" onclick="switchView('contacts', this); return false;">
                        <i class="fa-solid fa-envelope"></i>
                        <span class="nav-label">Quản lý Liên hệ</span>
                        <?php if (!empty($unreadContacts)): ?>
                            <span class="nav-count-badge" id="contact-unread-badge" title="Tin liên hệ mới"><?php echo (int) $unreadContacts; ?></span>
                        <?php else: ?>
                            <span class="nav-count-badge d-none" id="contact-unread-badge" title="Tin liên hệ mới">0</span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#chats" data-view="chats" class="nav-link" onclick="switchView('chats', this); return false;">
                        <i class="fa-solid fa-comments"></i>
                        <span class="nav-label">Quản lý Chat Box</span>
                        <?php if (!empty($unreadChats)): ?>
                            <span class="nav-count-badge" id="chat-unread-badge-sidebar" title="Tin nhắn chưa đọc"><?php echo (int) $unreadChats; ?></span>
                        <?php else: ?>
                            <span class="nav-count-badge d-none" id="chat-unread-badge-sidebar" title="Tin nhắn chưa đọc">0</span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#users" data-view="users" class="nav-link" onclick="switchView('users', this); return false;">
                        <i class="fa-solid fa-users"></i> Quản lý User
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#categories" data-view="categories" class="nav-link" onclick="switchView('categories', this); return false;">
                        <i class="fa-solid fa-list-ul"></i> Quản lý Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#blogs" data-view="blogs" class="nav-link" onclick="switchView('blogs', this); return false;">
                        <i class="fa-solid fa-newspaper"></i> Quản lý Tin tức
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#settings" data-view="settings" class="nav-link" onclick="switchView('settings', this); return false;">
                        <i class="fa-solid fa-gear"></i> Cấu hình Website
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#indexing" data-view="indexing" class="nav-link" onclick="switchView('indexing', this); return false;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Quản lý Index
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#keywords" data-view="keywords" class="nav-link" onclick="switchView('keywords', this); return false;">
                        <i class="fa-solid fa-key"></i> Quản lý Từ khóa SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#security-logs" data-view="security-logs" class="nav-link text-danger fw-bold" onclick="switchView('security-logs', this); return false;">
                        <i class="fa-solid fa-shield-halved"></i> Log An Ninh & Session
                    </a>
                </li>

            </ul>
            <div class="sidebar-user-footer">
                <div class="d-flex align-items-center text-white">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=fff&color=000"
                        class="rounded-circle me-2 flex-shrink-0" width="34" height="34" alt="Avatar">
                    <div style="font-size: 0.82rem; min-width: 0;" class="flex-grow-1">
                        <div class="fw-bold text-white text-truncate"><?php echo htmlspecialchars($currentUser['name'] ?? 'Admin'); ?></div>
                        <div class="text-truncate" style="color: #94a3b8 !important; font-size: 0.72rem;">
                            <?php echo htmlspecialchars($currentUser['email'] ?? 'admin@aicualtoi.com'); ?></div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light border shadow-sm me-1 d-lg-none" onclick="toggleSidebar()" aria-label="Toggle Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h5 class="mb-0 fw-bold" id="page-title">Tổng quan</h5>
                </div>
                <div class="d-flex align-items-center gap-1 gap-md-2">
                    <button class="btn btn-light border-0 shadow-sm" title="Thông báo"><i class="fa-regular fa-bell"></i></button>
                    <a href="index.php?action=logout" class="btn btn-light border shadow-sm" title="Đăng xuất">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="d-none d-sm-inline ms-1 ms-md-2">Xem Website</span>
                    </a>
                </div>
            </header>

            <div class="content-area">

                <div id="view-products" class="view-section">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Danh sách Dịch vụ / Sản phẩm</h6>
                                <small class="text-muted">Quản lý các sản phẩm hiển thị trên trang chủ</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light border" onclick="pushIndexAll()">
                                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Push index
                                </button>
                                <button class="btn btn-black" onclick="openProductModal()">
                                    <i class="fa-solid fa-plus me-1"></i> Thêm mới
                                </button>
                            </div>
                        </div>

                        <!-- Filter & Search Toolbar -->
                        <div class="p-3 border-bottom bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="product-search-input" class="form-control border-start-0" placeholder="Tìm kiếm sản phẩm theo tên, tính năng..." oninput="handleProductSearch()">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <select class="form-select form-select-sm" id="product-filter-category" onchange="handleProductFilter()">
                                        <option value="">Tất cả danh mục</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <select class="form-select form-select-sm" id="product-filter-status" onchange="handleProductFilter()">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="active">Đang bán</option>
                                        <option value="out_of_stock">Hết hàng</option>
                                        <option value="hidden">Đã ẩn</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2 text-md-end">
                                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="resetProductFilter()">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Đặt lại
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-custom align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá bán</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="product-table-body">
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light flex-wrap gap-2">
                            <span class="small text-muted" id="product-pagination-info">
                                Hiển thị <span id="product-count-start" class="fw-bold text-dark">0</span> - <span id="product-count-end" class="fw-bold text-dark">0</span> / tổng số <span id="product-count-total" class="fw-bold text-dark">0</span> sản phẩm
                            </span>
                            <nav aria-label="Products navigation">
                                <ul class="pagination pagination-sm mb-0 d-flex align-items-center gap-1" id="product-pagination-container">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div id="view-orders" class="view-section">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Danh sách Đơn hàng</h6>
                                <small class="text-muted">Quản lý trạng thái đơn hàng và giao hàng thủ công cho khách hàng</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-light border" onclick="fetchOrders(ordersCurrentPage)">
                                    <i class="fa-solid fa-rotate me-1"></i> Làm mới
                                </button>
                            </div>
                        </div>

                        <!-- Filter & Search Toolbar -->
                        <div class="p-3 border-bottom bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="order-search-input" class="form-control border-start-0" placeholder="Tìm theo mã đơn, email, SĐT, tên sản phẩm, mã GD..." onkeydown="if(event.key==='Enter'){handleOrderFilter();}">
                                        <button class="btn btn-outline-secondary" type="button" onclick="handleOrderFilter()">Tìm</button>
                                    </div>
                                </div>
                                <div class="col-8 col-md-4">
                                    <select class="form-select form-select-sm" id="order-filter-status" onchange="handleOrderFilter()">
                                        <option value="">Tất cả trạng thái đơn</option>
                                        <option value="pending">Chờ thanh toán (Pending)</option>
                                        <option value="processing">Đang xử lý (Processing)</option>
                                        <option value="completed">Thành công (Completed)</option>
                                        <option value="cancelled">Đã hủy (Cancelled)</option>
                                    </select>
                                </div>
                                <div class="col-4 col-md-2 text-end">
                                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="resetOrderFilter()">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Đặt lại
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-custom align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Sản phẩm / Gói</th>
                                        <th>Số tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày mua</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="order-table-body">
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light flex-wrap gap-2">
                            <span class="small text-muted" id="orders-pagination-info">
                                Hiển thị trang <span id="orders-current-page" class="fw-bold text-dark">1</span> / <span id="orders-total-pages" class="fw-bold text-dark">1</span> (Tổng <span id="orders-total-count" class="fw-bold text-dark">0</span> đơn)
                            </span>
                            <nav aria-label="Orders navigation">
                                <ul class="pagination pagination-sm mb-0 d-flex align-items-center gap-1" id="orders-pagination-list">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div id="view-contacts" class="view-section">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Tin nhắn liên hệ</h6>
                                <small class="text-muted">Các yêu cầu khách gửi từ trang Liên hệ</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-light border" onclick="renderContacts()">
                                    <i class="fa-solid fa-rotate me-1"></i> Làm mới
                                </button>
                            </div>
                        </div>

                        <!-- Filter Toolbar -->
                        <div class="p-3 border-bottom bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="contact-search-input" class="form-control border-start-0" placeholder="Tìm theo người gửi, email, chủ đề, nội dung..." oninput="handleContactSearch()">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 d-flex justify-content-md-end gap-1 flex-wrap">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-dark contact-tab-btn active" data-status="all" onclick="filterContactsByStatus('all', this)">Tất cả</button>
                                        <button type="button" class="btn btn-outline-dark contact-tab-btn" data-status="new" onclick="filterContactsByStatus('new', this)">Mới</button>
                                        <button type="button" class="btn btn-outline-dark contact-tab-btn" data-status="read" onclick="filterContactsByStatus('read', this)">Đã đọc</button>
                                        <button type="button" class="btn btn-outline-dark contact-tab-btn" data-status="archived" onclick="filterContactsByStatus('archived', this)">Lưu trữ</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-custom align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Khách gửi</th>
                                        <th>Chủ đề</th>
                                        <th>Nội dung</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày gửi</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="contact-table-body"></tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light flex-wrap gap-2">
                            <span class="small text-muted" id="contact-pagination-info">
                                Hiển thị <span id="contact-count-start" class="fw-bold text-dark">0</span> - <span id="contact-count-end" class="fw-bold text-dark">0</span> / tổng số <span id="contact-count-total" class="fw-bold text-dark">0</span> tin nhắn
                            </span>
                            <nav aria-label="Contacts navigation">
                                <ul class="pagination pagination-sm mb-0 d-flex align-items-center gap-1" id="contact-pagination-container">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div id="view-categories" class="view-section">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Quản lý Danh mục (Pill Menu)</h6>
                                <small class="text-muted">Cấu hình các nút lọc sản phẩm trên trang chủ</small>
                            </div>
                            <button class="btn btn-black" onclick="openCategoryModal()">
                                <i class="fa-solid fa-plus me-1"></i> Thêm danh mục
                            </button>
                        </div>
                        <div class="p-3 border-bottom bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="category-search-input" class="form-control border-start-0" placeholder="Lọc nhanh danh mục theo tên, slug..." oninput="handleCategorySearch()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-custom align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Tên danh mục</th>
                                        <th>Slug (Lọc)</th>
                                        <th>Hiệu ứng PRO</th>
                                        <th>Icon</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="category-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="view-users" class="view-section">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Quản lý User</h6>
                                <small class="text-muted">Sửa thông tin, block/unblock, reset mật khẩu và dọn dẹp user spam.</small>
                            </div>
                            <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="deleteBlockedUsers()">
                                <i class="fa-solid fa-broom me-1"></i> Xoá tất cả User bị Block (Spam)
                            </button>
                        </div>

                        <!-- Filter & Search Toolbar -->
                        <div class="p-3 border-bottom bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="user-search-input" class="form-control border-start-0" placeholder="Tìm kiếm theo tên, email user..." oninput="handleUserSearch()">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <select class="form-select form-select-sm" id="user-filter-role" onchange="handleUserFilter()">
                                        <option value="">Tất cả quyền (Role)</option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <select class="form-select form-select-sm" id="user-filter-status" onchange="handleUserFilter()">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="active">Active</option>
                                        <option value="blocked">Blocked</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2 text-md-end">
                                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="resetUserFilter()">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Đặt lại
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-custom align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Quyền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="user-table-body"></tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light flex-wrap gap-2">
                            <span class="small text-muted" id="user-pagination-info">
                                Hiển thị <span id="user-count-start" class="fw-bold text-dark">0</span> - <span id="user-count-end" class="fw-bold text-dark">0</span> / tổng số <span id="user-count-total" class="fw-bold text-dark">0</span> user
                            </span>
                            <nav aria-label="Users navigation">
                                <ul class="pagination pagination-sm mb-0 d-flex align-items-center gap-1" id="user-pagination-container">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div id="view-security-logs" class="view-section">
                    <div class="row">
                        <!-- Realtime Active Sessions (Online Users) -->
                        <div class="col-lg-12 mb-4">
                            <div class="card-custom border-success border-top" style="border-width: 4px !important;">
                                <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-success">
                                            <span class="spinner-grow spinner-grow-sm text-success me-2" role="status"></span>
                                            Phiên Đang Hoạt Động (Realtime Online - Auto 5s)
                                        </h6>
                                        <small class="text-muted">Danh sách các phên (Khách vãng lai & User) đang lướt web trong 2 phút vừa qua.</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success rounded-pill px-3 py-2" id="active-sessions-count-badge">0 Đang Online</span>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="loadSecurityLogs()">
                                            <i class="fa-solid fa-rotate me-1"></i> Cập nhật
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-custom mb-0">
                                        <thead>
                                            <tr>
                                                <th>Đối tượng / Session</th>
                                                <th>IP</th>
                                                <th>Trang Đang Xem / URL Gõ</th>
                                                <th>Lần Cuối Xuất Hiện</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody id="active-sessions-table-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Banned IPs List -->
                        <div class="col-lg-12 mb-4">
                            <div class="card-custom border-danger border-top" style="border-width: 4px !important;">
                                <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-muted"><i class="fa-solid fa-ban me-2"></i>Chặn / Khóa IP (Đã Tắt)</h6>
                                        <small class="text-muted">Cơ chế chặn và khóa IP đã được tắt hoàn toàn theo yêu cầu hệ thống.</small>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-custom mb-0">
                                        <thead>
                                            <tr>
                                                <th>Địa chỉ IP</th>
                                                <th>Lý do Block</th>
                                                <th>URL / Payload Dò Thăm</th>
                                                <th>Thời gian Block</th>
                                                <th class="text-end">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody id="banned-ips-table-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Activity History & Audit Logs -->
                        <div class="col-lg-12">
                            <div class="card-custom">
                                <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-muted"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch Sử Thao Tác (Đã Tắt Ghi Log)</h6>
                                        <small class="text-muted">Ghi nhận log thao tác đã được tắt để tối ưu tốc độ và dung lượng hệ thống.</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger" onclick="clearSecurityHistoryLogs()">
                                        <i class="fa-solid fa-trash me-1"></i> Xóa Sạch Lịch Sử
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-custom mb-0">
                                        <thead>
                                            <tr>
                                                <th>Thời gian</th>
                                                <th>Đối tượng / Session</th>
                                                <th>Hành động</th>
                                                <th>URL & Nội dung gõ</th>
                                                <th>IP</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody id="security-logs-table-body"></tbody>
                                    </table>
                                </div>
                                <div class="card-footer bg-light border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span class="small text-muted" id="security-logs-pagination-info">
                                        Hiển thị <span id="security-logs-count-start" class="fw-bold text-dark">0</span> - <span id="security-logs-count-end" class="fw-bold text-dark">0</span> / tổng số <span id="security-logs-count-total" class="fw-bold text-dark">0</span> log
                                    </span>
                                    <nav aria-label="Security logs navigation">
                                        <ul class="pagination pagination-sm mb-0 d-flex align-items-center gap-1" id="security-logs-pagination-container">
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view-blogs" class="view-section">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Danh sách Tin tức</h6>
                                <small class="text-muted">Các bài viết hiển thị ở phần Tạp chí trên trang chủ</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-black" onclick="openBlogModal()">
                                    <i class="fa-solid fa-plus me-1"></i> Viết bài mới
                                </button>
                            </div>
                        </div>

                        <!-- Filter Toolbar -->
                        <div class="p-3 border-bottom bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="blog-search-input" class="form-control border-start-0" placeholder="Tìm bài viết theo tiêu đề..." oninput="handleBlogSearch()">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-custom align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Bài viết</th>
                                        <th>Ngày đăng</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="blog-table-body">
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light flex-wrap gap-2">
                            <span class="small text-muted" id="blog-pagination-info">
                                Hiển thị <span id="blog-count-start" class="fw-bold text-dark">0</span> - <span id="blog-count-end" class="fw-bold text-dark">0</span> / tổng số <span id="blog-count-total" class="fw-bold text-dark">0</span> bài viết
                            </span>
                            <nav aria-label="Blogs navigation">
                                <ul class="pagination pagination-sm mb-0 d-flex align-items-center gap-1" id="blog-pagination-container">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div id="view-indexing" class="view-section">
                    <!-- Google Indexing API Status Bar -->
                    <div class="card-custom p-3 p-md-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #09090b 0%, #18181b 100%); color: #fff;">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3);">
                                    <i class="fa-solid fa-cloud-arrow-up text-success fs-5"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold mb-0 text-white">Google Indexing API v3</h5>
                                        <span class="badge bg-success rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-circle text-white me-1" style="font-size: 0.5rem;"></i> Đang hoạt động
                                        </span>
                                    </div>
                                    <div class="small text-white-50 mt-1 d-flex flex-wrap align-items-center gap-3">
                                        <span><i class="fa-regular fa-id-badge me-1"></i> Bot: <strong class="text-white">aicuatoi-bot@tuanpp.iam.gserviceaccount.com</strong></span>
                                        <span><i class="fa-solid fa-globe me-1"></i> Canonical: <strong class="text-white">https://aicuatoi.net</strong></span>
                                        <span><i class="fa-solid fa-gauge-high me-1"></i> Quota: <strong class="text-warning">200 URL/ngày</strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="refreshIndexingCounts()">
                                    <i class="fa-solid fa-rotate me-1"></i> Cập nhật số liệu
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- Manual Indexing Card -->
                        <div class="col-lg-6">
                            <div class="card-custom p-4 h-100 border shadow-sm bg-white">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <h5 class="fw-bold mb-0 text-dark">
                                        <i class="fa-solid fa-paper-plane text-primary me-2"></i>Index URL Thủ Công
                                    </h5>
                                    <span class="badge bg-light text-muted border rounded-pill">Đơn lẻ</span>
                                </div>
                                <p class="text-muted small mb-2">Nhập URL trực tiếp hoặc bấm các nút thêm nhanh trang chính:</p>

                                <!-- Quick Chips -->
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    <button type="button" class="btn btn-light btn-sm border rounded-pill px-2.5 py-1" style="font-size: 0.78rem;" onclick="addManualUrl('https://aicuatoi.net/')">
                                        + Trang chủ
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm border rounded-pill px-2.5 py-1" style="font-size: 0.78rem;" onclick="addManualUrl('https://aicuatoi.net/san-pham')">
                                        + Sản phẩm
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm border rounded-pill px-2.5 py-1" style="font-size: 0.78rem;" onclick="addManualUrl('https://aicuatoi.net/tap-chi')">
                                        + Tạp chí
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm border rounded-pill px-2.5 py-1" style="font-size: 0.78rem;" onclick="addManualUrl('https://aicuatoi.net/gioi-thieu')">
                                        + Giới thiệu
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm border rounded-pill px-2.5 py-1" style="font-size: 0.78rem;" onclick="addManualUrl('https://aicuatoi.net/lien-he')">
                                        + Liên hệ
                                    </button>
                                </div>

                                <div id="dashboard-index-url-rows" class="d-grid gap-2 mb-3">
                                    <div class="input-group dashboard-index-url-row">
                                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-link"></i></span>
                                        <input type="url" class="form-control dashboard-index-url-input border-start-0" placeholder="https://aicuatoi.net/tim-kiem/gpt" value="https://aicuatoi.net/tim-kiem/gpt">
                                        <button class="btn btn-light border text-danger" type="button" onclick="removeDashboardIndexUrlRow(this)" title="Xóa hàng">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap pt-3 border-top">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-light btn-sm border" onclick="addDashboardIndexUrlRow()">
                                            <i class="fa-solid fa-plus me-1 text-success"></i> Thêm ô
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm border" onclick="pasteMultipleUrlsPrompt()">
                                            <i class="fa-solid fa-paste me-1 text-primary"></i> Dán nhiều URL
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold" id="btn-manual-submit" onclick="pushDashboardIndexUrls()">
                                        <i class="fa-solid fa-paper-plane me-1.5"></i> Gửi Index ngay
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Indexing Card -->
                        <div class="col-lg-6">
                            <div class="card-custom p-4 h-100 border shadow-sm bg-white">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <h5 class="fw-bold mb-0 text-dark">
                                        <i class="fa-solid fa-bolt text-warning me-2"></i>Index Hàng Loạt Tự Động
                                    </h5>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Batch Runner</span>
                                </div>
                                <p class="text-muted small mb-3">Gửi yêu cầu Google Indexing API mượt mà theo từng đợt, hiển thị tiến trình trực tiếp:</p>

                                <div class="d-grid gap-2 mb-3">
                                    <button class="btn btn-outline-dark btn-sm text-start d-flex align-items-center justify-content-between p-2.5 rounded-3 bulk-btn" onclick="startBatchIndexing('products')">
                                        <span><i class="fa-solid fa-box text-primary me-2"></i> Toàn bộ sản phẩm</span>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1" id="idx-count-products">...</span>
                                    </button>
                                    <button class="btn btn-outline-dark btn-sm text-start d-flex align-items-center justify-content-between p-2.5 rounded-3 bulk-btn" onclick="startBatchIndexing('categories')">
                                        <span><i class="fa-solid fa-list-ul text-info me-2"></i> Toàn bộ danh mục</span>
                                        <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1" id="idx-count-categories">...</span>
                                    </button>
                                    <button class="btn btn-outline-dark btn-sm text-start d-flex align-items-center justify-content-between p-2.5 rounded-3 bulk-btn" onclick="startBatchIndexing('blogs')">
                                        <span><i class="fa-solid fa-newspaper text-success me-2"></i> Toàn bộ bài viết tin tức</span>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1" id="idx-count-blogs">...</span>
                                    </button>
                                    <button class="btn btn-outline-dark btn-sm text-start d-flex align-items-center justify-content-between p-2.5 rounded-3 bulk-btn" onclick="startBatchIndexing('keywords')">
                                        <span><i class="fa-solid fa-tags text-purple me-2"></i> Toàn bộ từ khóa SEO</span>
                                        <span class="badge bg-secondary-subtle text-dark rounded-pill px-2.5 py-1" id="idx-count-keywords">...</span>
                                    </button>
                                </div>

                                <button class="btn btn-success btn-sm w-100 fw-bold py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-between px-3" onclick="startBatchIndexing('all')">
                                    <span><i class="fa-solid fa-cloud-arrow-up text-white me-2"></i> Index toàn bộ hệ thống (Tất cả URL)</span>
                                    <span class="badge bg-white text-success rounded-pill px-2.5 py-1" id="idx-count-all">...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Live Progress & Log Terminal Card -->
                    <div class="card-custom p-4 border shadow-sm bg-white mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-terminal text-dark"></i>
                                    <span>Bảng Tiến Trình & Live Console</span>
                                </h6>
                                <small class="text-muted" id="indexing-live-status">Sẵn sàng thực hiện yêu cầu index.</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" id="btn-stop-indexing" onclick="stopBatchIndexing()" style="display: none;">
                                    <i class="fa-solid fa-stop me-1"></i> Dừng lại
                                </button>
                                <button type="button" class="btn btn-light btn-sm border rounded-pill px-3" onclick="clearIndexingConsole()">
                                    <i class="fa-solid fa-eraser me-1"></i> Xóa log
                                </button>
                            </div>
                        </div>

                        <!-- Counters Row -->
                        <div class="row g-3 text-center mb-3">
                            <div class="col-3">
                                <div class="p-2 border rounded-3 bg-light">
                                    <div class="small text-muted">Tổng URL</div>
                                    <div class="fs-5 fw-bold text-dark" id="console-stat-total">0</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 border rounded-3 bg-light">
                                    <div class="small text-muted">Đang xử lý</div>
                                    <div class="fs-5 fw-bold text-primary" id="console-stat-processed">0</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 border rounded-3 bg-light">
                                    <div class="small text-muted">Thành công (200)</div>
                                    <div class="fs-5 fw-bold text-success" id="console-stat-success">0</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 border rounded-3 bg-light">
                                    <div class="small text-muted">Thất bại</div>
                                    <div class="fs-5 fw-bold text-danger" id="console-stat-failed">0</div>
                                </div>
                            </div>
                        </div>

                        <!-- Animated Progress Bar -->
                        <div class="progress mb-3" style="height: 10px; border-radius: 6px; background-color: #f1f5f9;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="indexing-progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Dark Terminal Console -->
                        <div class="indexing-terminal p-3 rounded-3" id="indexing-terminal" style="background: #09090b; color: #a1a1aa; font-family: 'Consolas', 'Monaco', monospace; font-size: 0.82rem; height: 260px; overflow-y: auto; border: 1px solid #27272a;">
                            <div class="text-secondary">// Google Indexing Console sẵn sàng. Bấm bắt đầu để xem tiến trình từng URL...</div>
                        </div>
                    </div>
                </div>

                <div id="view-keywords" class="view-section">
                    <div class="row">
                        <!-- Keyword Manager -->
                        <div class="col-12 mb-4">
                            <div class="card-custom h-100 border-primary border-top" style="border-width: 4px !important;">
                                <div class="card-header-custom border-bottom-0 pb-0">
                                    <div>
                                        <h5 class="fw-bold mb-1"><i class="fa-solid fa-key text-primary me-2"></i>Quản lý Từ khóa SEO</h5>
                                        <small class="text-muted">Quản lý từ khóa (URL tĩnh, metadata SEO, aliases) lưu trong file JSON.</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="openBulkKeywordModal()">
                                            <i class="fa-solid fa-file-import me-1"></i> Nhập hàng loạt
                                        </button>
                                        <button class="btn btn-black btn-sm" onclick="openKeywordModal()">
                                            <i class="fa-solid fa-plus me-1"></i> Thêm từ khóa
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive p-3">
                                    <table class="table table-hover table-custom mb-0" style="border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
                                        <thead>
                                            <tr>
                                                <th>Từ khóa (Slug)</th>
                                                <th>Tên hiển thị</th>
                                                <th>Mô tả SEO</th>
                                                <th>Aliases</th>
                                                <th class="text-end">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody id="keyword-table-body">
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Đang tải danh sách từ khóa...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
                                    <span class="small text-muted">
                                        Hiển thị trang <span id="keywords-current-page" class="fw-bold text-dark">1</span> / <span id="keywords-total-pages" class="fw-bold text-dark">1</span>
                                    </span>
                                    <nav aria-label="Keywords navigation">
                                        <ul class="pagination pagination-sm mb-0" style="gap:4px; list-style:none; padding-left:0;">
                                            <li>
                                                <button class="btn btn-sm btn-outline-dark me-1 px-3" onclick="changeKeywordsPage(-1)" id="keywords-btn-prev">Trước</button>
                                            </li>
                                            <li>
                                                <button class="btn btn-sm btn-outline-dark px-3" onclick="changeKeywordsPage(1)" id="keywords-btn-next">Sau</button>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view-settings" class="view-section">

                    <div class="row">
                        <div class="col-12">
                            <div class="card-custom p-4 mb-4 border-primary border-top"
                                style="border-width: 4px !important;">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-bolt"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0">Tích hợp SePay (Tự động duyệt nạp tiền)</h5>
                                </div>

                                <div class="alert alert-warning border-0 rounded-3 d-flex align-items-center justify-content-between mb-3 py-2">
                                    <div>
                                        <i class="fa-solid fa-flask text-warning me-2"></i>
                                        <strong>Chế độ Demo</strong> — Hiển thị nút "Thanh toán thành công" giả lập trên trang QR cho khách hàng test mua hàng. Tắt khi đã chạy thật.
                                    </div>
                                    <div class="form-check form-switch mb-0 ms-3">
                                        <input class="form-check-input" type="checkbox" id="st_demo_payment_active"
                                            <?= ($settings['demo_payment_active'] ?? '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="st_demo_payment_active">Bật demo</label>
                                    </div>
                                </div>

                                <form id="sepaySettingsForm">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label d-block">Trạng thái SePay</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="st_sepay_active"
                                                    <?= ($settings['sepay_active'] ?? '0') == '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="st_sepay_active">Kích hoạt SePay
                                                    Checkout</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Chế độ</label>
                                            <select class="form-select" id="st_sepay_mode">
                                                <option value="production" <?= ($settings['sepay_mode'] ?? 'production') == 'production' ? 'selected' : '' ?>>Production (Thật)
                                                </option>
                                                <option value="sandbox" <?= ($settings['sepay_mode'] ?? '') == 'sandbox' ? 'selected' : '' ?>>Sandbox (Thử nghiệm)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Secret Key / Webhook Token (Bảo mật)</label>
                                            <input type="text" class="form-control" id="st_sepay_token"
                                                value="<?php echo htmlspecialchars($settings['sepay_token'] ?? ''); ?>"
                                                placeholder="Nhập Secret Key hoặc Webhook Token từ SePay">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SePay Merchant ID</label>
                                            <input type="text" class="form-control" id="st_sepay_merchant_id"
                                                value="<?php echo htmlspecialchars($settings['sepay_merchant_id'] ?? ''); ?>"
                                                placeholder="Ví dụ: SP-LIVE-XXXXXX">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SePay API Key</label>
                                            <input type="password" class="form-control" id="st_sepay_api_key"
                                                value="<?php echo htmlspecialchars($settings['sepay_api_key'] ?? ''); ?>"
                                                placeholder="spsk_live_...">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Ngân hàng (VietQR ID)</label>
                                            <input type="text" class="form-control" id="st_bank_id"
                                                value="<?php echo htmlspecialchars($settings['bank_id'] ?? ''); ?>"
                                                placeholder="Ví dụ: KienLongBank, OCB, MBBank, VCB...">
                                            <small class="text-muted">Xem mã ngân hàng tại vietqr.io</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Số tài khoản nhận tiền</label>
                                            <input type="text" class="form-control" id="st_bank_account"
                                                value="<?php echo htmlspecialchars($settings['bank_account'] ?? ''); ?>"
                                                placeholder="Nhập số tài khoản nhận tiền">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tên chủ tài khoản</label>
                                            <input type="text" class="form-control" id="st_bank_name"
                                                value="<?php echo htmlspecialchars($settings['bank_name'] ?? ''); ?>"
                                                placeholder="NGUYEN VAN A">
                                        </div>
                                    </div>
                                    <div class="bg-light p-3 rounded-3 mt-2 border-start border-primary border-4">
                                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-circle-info text-primary"></i>
                                                <span class="small fw-bold">Webhook URL:</span>
                                            </div>
                                            <code class="text-danger text-break"><?= url('index.php?action=sepayWebhook') ?></code>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 mt-md-0" onclick="loadSePayDebug()">
                                                <i class="fa-solid fa-bug me-1"></i>Kiểm tra webhook cuối
                                            </button>
                                        </div>
                                        <pre id="sepay-debug-output" class="small bg-white border rounded-3 p-3 mt-3 mb-0 d-none"
                                            style="white-space:pre-wrap;max-height:260px;overflow:auto;"></pre>
                                        <small class="text-muted d-block mt-1">Cấu hình Webhook URL này trên dashboard
                                            SePay để nhận thông báo thanh toán tự động.</small>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card-custom p-4 mb-4">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">Trang Giới Thiệu (About Us)</h5>
                                <form id="aboutSettingsForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tiêu đề chính</label>
                                            <input type="text" class="form-control" id="st_about_title"
                                                value="<?php echo htmlspecialchars($settings['about_title'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Hình ảnh minh họa (URL)</label>
                                            <input type="text" class="form-control" id="st_about_image"
                                                value="<?php echo htmlspecialchars($settings['about_image'] ?? ''); ?>">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Mô tả ngắn</label>
                                            <textarea class="form-control" id="st_about_desc"
                                                rows="2"><?php echo htmlspecialchars($settings['about_desc'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Số liệu nổi bật (vd: 50K+)</label>
                                            <input type="text" class="form-control" id="st_about_stat_value"
                                                value="<?php echo htmlspecialchars($settings['about_stat_value'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nhãn số liệu (vd: Khách hàng tin dùng...)</label>
                                            <input type="text" class="form-control" id="st_about_stat_label"
                                                value="<?php echo htmlspecialchars($settings['about_stat_label'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Các tính năng nổi bật (Features)</label>
                                            <button type="button" class="btn btn-sm btn-outline-dark"
                                                onclick="addAboutFeatureRow()">
                                                <i class="fa-solid fa-plus me-1"></i> Thêm tính năng
                                            </button>
                                        </div>
                                        <div id="about-features-container" class="bg-light p-3 rounded-3 border">
                                            <!-- Feature rows -->
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card-custom p-4 mb-4">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">Trang Liên Hệ (Contact)</h5>
                                <form id="contactSettingsForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tiêu đề liên hệ</label>
                                            <input type="text" class="form-control" id="st_contact_title"
                                                value="<?php echo htmlspecialchars($settings['contact_title'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Mô tả liên hệ</label>
                                            <input type="text" class="form-control" id="st_contact_desc"
                                                value="<?php echo htmlspecialchars($settings['contact_desc'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Phương thức liên hệ (Icon + Text)</label>
                                            <button type="button" class="btn btn-sm btn-outline-dark"
                                                onclick="addContactMethodRow()">
                                                <i class="fa-solid fa-plus me-1"></i> Thêm phương thức
                                            </button>
                                        </div>
                                        <div id="contact-methods-container" class="bg-light p-3 rounded-3 border">
                                            <!-- Method rows -->
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Mạng xã hội (Social Links)</label>
                                            <button type="button" class="btn btn-sm btn-outline-dark"
                                                onclick="addSocialLinkRow()">
                                                <i class="fa-solid fa-plus me-1"></i> Thêm MXH
                                            </button>
                                        </div>
                                        <div id="social-links-container" class="bg-light p-3 rounded-3 border">
                                            <!-- Social rows -->
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-custom p-4 mb-4">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">Header & Mini Banner</h5>
                                <form id="headerSettingsForm">
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả ngắn trang chủ (Hero)</label>
                                        <textarea class="form-control" id="st_heroDesc" rows="3"><?php echo htmlspecialchars($settings['heroDesc'] ?? 'Chào mừng bạn đến với AI CỦA TÔI - nền tảng hàng đầu cung cấp các tài khoản Premium (ChatGPT Plus, Claude Pro, Midjourney, YouTube Premium, GitHub Copilot...) tự động 24/7. Uy tín, an toàn, kích hoạt ngay lập tức với chế độ bảo hành 1 đổi 1 trọn gói.'); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Dòng chữ Mini Banner</label>
                                        <input type="text" class="form-control" id="st_bannerText"
                                            value="<?php echo htmlspecialchars($settings['bannerText'] ?? ''); ?>">
                                        <small class="text-muted">Hiển thị dòng chữ nhỏ chạy trên cùng web.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại Zalo/Hotline</label>
                                        <input type="text" class="form-control" id="st_zalo"
                                            value="<?php echo htmlspecialchars($settings['zalo'] ?? ''); ?>">
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card-custom p-4 mb-4">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">Nội dung Footer</h5>
                                <form id="footerSettingsForm">
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả ngắn (Giới thiệu web)</label>
                                        <textarea class="form-control" id="st_footerDesc"
                                            rows="3"><?php echo htmlspecialchars($settings['footerDesc'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link Facebook/Twitter (Tùy chọn)</label>
                                        <input type="text" class="form-control" id="st_socialLink"
                                            placeholder="https://..."
                                            value="<?php echo htmlspecialchars($settings['socialLink'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tên bản quyền (Copyright)</label>
                                        <input type="text" class="form-control" id="st_copyright"
                                            value="<?php echo htmlspecialchars($settings['copyright'] ?? ''); ?>">
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <label class="form-label">Điều khoản dịch vụ</label>
                                        <textarea class="form-control" id="st_terms_of_service"
                                            rows="5"><?php echo htmlspecialchars($settings['terms_of_service'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Chính sách bảo mật</label>
                                        <textarea class="form-control" id="st_privacy_policy"
                                            rows="5"><?php echo htmlspecialchars($settings['privacy_policy'] ?? ''); ?></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-black px-5 py-2 fs-6" onclick="saveSettings()">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu toàn bộ cấu hình
                        </button>
                    </div>

                    <!-- Telegram Notification Settings -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card-custom p-4 mb-4" style="border: 2px solid #e0e7ff; background: linear-gradient(135deg, rgba(99,102,241,0.04) 0%, rgba(168,85,247,0.04) 100%);">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#a855f7);display:flex;align-items:center;justify-content:center;">
                                        <i class="fa-brands fa-telegram text-white fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Thông báo Telegram</h5>
                                        <small class="text-muted">Admin sẽ nhận tin nhắn khi có đơn hàng hoặc khách chat</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Bot Token <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-robot text-muted"></i></span>
                                            <input type="password" class="form-control border-start-0" id="st_telegram_bot_token"
                                                placeholder="1234567890:ABCdef..."
                                                value="<?php echo htmlspecialchars($settings['telegram_bot_token'] ?? ''); ?>">
                                            <button class="btn btn-outline-secondary" type="button" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Được tạo từ <a href="https://t.me/BotFather" target="_blank">@BotFather</a> trên Telegram</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Chat ID của Admin <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-hashtag text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0" id="st_telegram_chat_id"
                                                placeholder="123456789 hoặc -100123456789"
                                                value="<?php echo htmlspecialchars($settings['telegram_chat_id'] ?? ''); ?>">
                                        </div>
                                        <small class="text-muted">Lấy từ <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a> hoặc <a href="https://t.me/myidbot" target="_blank">@myidbot</a></small>
                                    </div>
                                </div>

                                <div class="mt-3 p-3 rounded-3" style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.15);">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-auto">
                                            <i class="fa-solid fa-circle-info text-primary"></i>
                                        </div>
                                        <div class="col">
                                            <p class="mb-1 small fw-semibold">Hướng dẫn cài đặt:</p>
                                            <ol class="mb-0 small text-muted ps-3">
                                                <li>Mở Telegram, tìm <strong>@BotFather</strong>, gõ <code>/newbot</code> để tạo bot</li>
                                                <li>Sao chép <strong>Bot Token</strong> và dán vào ô bên trái</li>
                                                <li>Nhắn tin cho bot để khởi tạo, sau đó tìm <strong>@userinfobot</strong> để lấy <strong>Chat ID</strong></li>
                                                <li>Lưu cài đặt rồi bấm <strong>Test kết nối</strong></li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-black px-4" onclick="saveTelegramSettings()">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Telegram
                                    </button>
                                    <button class="btn btn-outline-primary px-4" id="btnTelegramTest" onclick="testTelegram()">
                                        <i class="fa-brands fa-telegram me-2"></i>Test kết nối
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email SMTP Settings -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card-custom p-4 mb-4" style="border: 2px solid #e0f2fe; background: linear-gradient(135deg, rgba(14,165,233,0.04) 0%, rgba(56,189,248,0.04) 100%);">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#0ea5e9,#38bdf8);display:flex;align-items:center;justify-content:center;">
                                        <i class="fa-solid fa-envelope text-white fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Cấu hình Email (SMTP)</h5>
                                        <small class="text-muted">Cấu hình tài khoản SMTP để gửi KEY/Tài khoản tự động hoặc thủ công qua Email</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">SMTP Host</label>
                                        <input type="text" class="form-control" id="st_smtp_host"
                                            placeholder="smtp.hostinger.com"
                                            value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">SMTP Port</label>
                                        <input type="number" class="form-control" id="st_smtp_port"
                                            placeholder="465"
                                            value="<?php echo htmlspecialchars($settings['smtp_port'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Mã hóa (Encryption)</label>
                                        <select class="form-select" id="st_smtp_secure">
                                            <option value="ssl" <?= ($settings['smtp_secure'] ?? 'ssl') == 'ssl' ? 'selected' : '' ?>>SSL (Khuyên dùng cho 465)</option>
                                            <option value="tls" <?= ($settings['smtp_secure'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS (Khuyên dùng cho 587)</option>
                                            <option value="none" <?= ($settings['smtp_secure'] ?? '') == 'none' ? 'selected' : '' ?>>Không mã hóa</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Tên người gửi (From Name)</label>
                                        <input type="text" class="form-control" id="st_smtp_from_name"
                                            placeholder="AI CỦA TÔI"
                                            value="<?php echo htmlspecialchars($settings['smtp_from_name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Tài khoản SMTP (Username)</label>
                                        <input type="text" class="form-control" id="st_smtp_user"
                                            placeholder="aicuatoi.net_no-reply@aicuatoi.net"
                                            value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Mật khẩu SMTP (Password)</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control border-end-0" id="st_smtp_pass"
                                                placeholder="••••••••"
                                                value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>">
                                            <button class="btn btn-outline-secondary" type="button" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Email gửi (From Email)</label>
                                        <input type="email" class="form-control" id="st_smtp_from_email"
                                            placeholder="aicuatoi.net_no-reply@aicuatoi.net"
                                            value="<?php echo htmlspecialchars($settings['smtp_from_email'] ?? ''); ?>">
                                    </div>
                                    
                                    <hr class="my-4">
                                    <h6 class="fw-bold mb-2">Mẫu Email bàn giao mặc định</h6>
                                    
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Tiêu đề Email mặc định</label>
                                        <input type="text" class="form-control" id="st_smtp_default_subject"
                                            placeholder="Bàn giao tài khoản / Key dịch vụ đơn hàng #{order_id}"
                                            value="<?php echo htmlspecialchars($settings['smtp_default_subject'] ?? 'Bàn giao tài khoản / Key dịch vụ đơn hàng #{order_id}'); ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Nội dung Email mặc định</label>
                                        <textarea class="form-control" id="st_smtp_default_body" rows="6" 
                                            placeholder="Chào bạn, đây là thông tin tài khoản / key cho đơn hàng #{order_id} của bạn:&#10;&#10;{delivered_accounts}&#10;&#10;Cảm ơn bạn đã mua hàng!"><?php echo htmlspecialchars($settings['smtp_default_body'] ?? "Chào bạn,\n\nĐây là thông tin tài khoản / key kích hoạt cho đơn hàng #{order_id} ({product_name}) của bạn:\n\n{delivered_accounts}\n\nCảm ơn bạn đã tin dùng dịch vụ của chúng tôi!\nNếu có bất kỳ câu hỏi nào, vui lòng liên hệ hỗ trợ.\nTrân trọng,\nBan quản trị."); ?></textarea>
                                        <small class="text-muted d-block mt-1">Sử dụng các biến sau để tự động điền: <code>#{order_id}</code> (Mã đơn), <code>{product_name}</code> (Tên sản phẩm), <code>{delivered_accounts}</code> (Tài khoản bàn giao nhập ở trên).</small>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button class="btn btn-black px-4" onclick="saveSmtpSettings()">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu cấu hình Email
                                    </button>
                                    <button class="btn btn-outline-primary px-4" id="btnSmtpTest" onclick="testSmtp()">
                                        <i class="fa-solid fa-paper-plane me-2"></i>Gửi thử Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view-dashboard" class="view-section">
                    <!-- Top Stat Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card-custom p-3 p-md-4 h-100 position-relative overflow-hidden mb-0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tổng Doanh Thu</div>
                                        <h3 class="fw-bold text-dark my-1" style="font-size: 1.45rem;">
                                            <?php echo number_format($totalRevenue ?? 0, 0, ',', '.') . 'đ'; ?>
                                        </h3>
                                    </div>
                                    <div class="rounded-3 p-2 text-white" style="background: linear-gradient(135deg, #111827 0%, #374151 100%); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-coins"></i>
                                    </div>
                                </div>
                                <div class="pt-2 border-top d-flex flex-wrap gap-2 align-items-center justify-content-between text-muted" style="font-size: 0.76rem;">
                                    <span>Hôm nay: <strong class="text-dark"><?php echo number_format($todayRevenue ?? 0, 0, ',', '.') . 'đ'; ?></strong></span>
                                    <span>Tháng này: <strong class="text-dark"><?php echo number_format($monthRevenue ?? 0, 0, ',', '.') . 'đ'; ?></strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card-custom p-3 p-md-4 h-100 position-relative overflow-hidden mb-0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tổng Đơn Hàng</div>
                                        <h3 class="fw-bold text-dark my-1" style="font-size: 1.45rem;">
                                            <?php echo number_format($totalOrders ?? 0); ?>
                                        </h3>
                                    </div>
                                    <div class="rounded-3 p-2 text-white bg-primary" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </div>
                                </div>
                                <div class="pt-2 border-top d-flex flex-wrap gap-2 align-items-center justify-content-between" style="font-size: 0.76rem;">
                                    <span class="text-success fw-medium"><i class="fa-solid fa-circle-check me-1"></i><?php echo number_format($completedOrders ?? 0); ?> thành công</span>
                                    <span class="badge <?php echo (!empty($pendingOrders) && $pendingOrders > 0) ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-light text-muted border'; ?>">
                                        <?php echo (int)($pendingOrders ?? 0); ?> cần xử lý
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card-custom p-3 p-md-4 h-100 position-relative overflow-hidden mb-0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Khách Hàng</div>
                                        <h3 class="fw-bold text-dark my-1" style="font-size: 1.45rem;">
                                            <?php echo number_format($totalCustomers ?? 0); ?>
                                        </h3>
                                    </div>
                                    <div class="rounded-3 p-2 text-white bg-info" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                </div>
                                <div class="pt-2 border-top d-flex flex-wrap gap-2 align-items-center justify-content-between text-muted" style="font-size: 0.76rem;">
                                    <span><i class="fa-solid fa-envelope me-1 text-secondary"></i>Khách mua hàng thực</span>
                                    <span>User: <strong class="text-dark"><?php echo count($users ?? []); ?></strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card-custom p-3 p-md-4 h-100 position-relative overflow-hidden mb-0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sản Phẩm & Dịch Vụ</div>
                                        <h3 class="fw-bold text-dark my-1" id="dash-total-products" style="font-size: 1.45rem;">
                                            <?php echo count($products ?? []); ?>
                                        </h3>
                                    </div>
                                    <div class="rounded-3 p-2 text-white bg-success" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-cubes"></i>
                                    </div>
                                </div>
                                <div class="pt-2 border-top d-flex flex-wrap gap-2 align-items-center justify-content-between text-muted" style="font-size: 0.76rem;">
                                    <span><i class="fa-solid fa-layer-group me-1 text-secondary"></i><?php echo count($categories ?? []); ?> danh mục</span>
                                    <?php if (!empty($unreadContacts)): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><?php echo (int)$unreadContacts; ?> tin mới</span>
                                    <?php else: ?>
                                        <span class="text-success"><i class="fa-solid fa-check me-1"></i>Hệ thống ổn định</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6-Month Chart & Monthly Breakdown Table -->
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-8">
                            <div class="card-custom p-4 h-100 mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-chart-column me-2 text-primary"></i>Biểu đồ Thống kê 6 tháng gần nhất</h6>
                                        <small class="text-muted">Doanh thu thực tế (VNĐ) và số đơn hoàn thành theo từng tháng</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-dark text-white px-2 py-1" style="font-size: 0.75rem;">
                                            <?php 
                                                $firstM = !empty($monthlyStats) ? $monthlyStats[0]['label'] : '';
                                                $lastM = !empty($monthlyStats) ? end($monthlyStats)['label'] : '';
                                                echo htmlspecialchars($firstM . ' — ' . $lastM);
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div style="position: relative; height: 320px; width: 100%;">
                                    <canvas id="dashboardMonthlyChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card-custom p-4 h-100 d-flex flex-column mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-table-list me-2 text-info"></i>Chi tiết Doanh số 6 tháng</h6>
                                </div>
                                <div class="table-responsive flex-grow-1">
                                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0">Tháng</th>
                                                <th class="border-0 text-end">Doanh thu</th>
                                                <th class="border-0 text-end">Đơn</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $sum6mRev = 0;
                                                $sum6mOrders = 0;
                                                if (!empty($monthlyStats)): 
                                                    foreach ($monthlyStats as $ms): 
                                                        $sum6mRev += (float)($ms['revenue'] ?? 0);
                                                        $sum6mOrders += (int)($ms['successful_orders'] ?? 0);
                                            ?>
                                                <tr>
                                                    <td class="fw-semibold text-dark">
                                                        <?php echo htmlspecialchars($ms['label'] ?? ''); ?>
                                                    </td>
                                                    <td class="text-end fw-bold text-dark">
                                                        <?php echo number_format($ms['revenue'] ?? 0, 0, ',', '.') . 'đ'; ?>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="badge bg-light text-dark border"><?php echo (int)($ms['successful_orders'] ?? 0); ?></span>
                                                    </td>
                                                </tr>
                                            <?php 
                                                    endforeach; 
                                                endif; 
                                            ?>
                                        </tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td class="text-dark">Tổng 6T</td>
                                                <td class="text-end text-success"><?php echo number_format($sum6mRev, 0, ',', '.') . 'đ'; ?></td>
                                                <td class="text-end"><span class="badge bg-success"><?php echo $sum6mOrders; ?></span></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: Recent Orders & Top Selling Products -->
                    <div class="row g-4 mb-4">
                        <!-- Recent 5 Orders -->
                        <div class="col-12 col-lg-7">
                            <div class="card-custom h-100 mb-0">
                                <div class="card-header-custom d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>5 Đơn hàng gần nhất</h6>
                                        <small class="text-muted">Các đơn hàng vừa phát sinh trên hệ thống</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="switchView('orders'); return false;">
                                        Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-3">Mã đơn</th>
                                                <th>Khách hàng</th>
                                                <th>Sản phẩm</th>
                                                <th class="text-end">Số tiền</th>
                                                <th class="text-center">Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($recentOrders)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">Chưa có đơn hàng nào</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($recentOrders as $ro): 
                                                    $roStatus = $ro['status'] ?? 'pending';
                                                    $roBadgeCls = $roStatus === 'completed' ? 'bg-success' 
                                                                : ($roStatus === 'processing' ? 'bg-primary' 
                                                                : ($roStatus === 'pending' ? 'bg-warning text-dark' : 'bg-danger'));
                                                    $roStatusLabel = $roStatus === 'completed' ? 'Thành công' 
                                                                   : ($roStatus === 'processing' ? 'Đang xử lý' 
                                                                   : ($roStatus === 'pending' ? 'Chờ thanh toán' : 'Đã hủy'));
                                                ?>
                                                    <tr>
                                                        <td class="px-3">
                                                            <a href="#" class="fw-bold text-dark text-decoration-none" onclick='viewOrderDetails(<?php echo htmlspecialchars(json_encode($ro), ENT_QUOTES, "UTF-8"); ?>); return false;'>
                                                                #<?php echo htmlspecialchars($ro['id']); ?>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <div class="fw-semibold text-truncate" style="max-width: 160px;" title="<?php echo htmlspecialchars($ro['customer_email'] ?? ''); ?>">
                                                                <?php echo htmlspecialchars($ro['customer_email'] ?? '—'); ?>
                                                            </div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($ro['phone'] ?? ''); ?></small>
                                                        </td>
                                                        <td>
                                                            <div class="text-truncate fw-medium" style="max-width: 170px;" title="<?php echo htmlspecialchars($ro['product_name'] ?? ''); ?>">
                                                                <?php echo htmlspecialchars($ro['product_name'] ?? '—'); ?>
                                                            </div>
                                                            <small class="text-muted"><?php echo !empty($ro['variant_name']) ? htmlspecialchars($ro['variant_name']) : ''; ?></small>
                                                        </td>
                                                        <td class="text-end fw-bold text-dark">
                                                            <?php echo number_format($ro['amount'] ?? 0, 0, ',', '.') . 'đ'; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge <?php echo $roBadgeCls; ?> rounded-pill" style="font-size: 0.72rem;">
                                                                <?php echo $roStatusLabel; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Top 5 Products -->
                        <div class="col-12 col-lg-5">
                            <div class="card-custom h-100 mb-0">
                                <div class="card-header-custom d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold"><i class="fa-solid fa-fire me-2 text-danger"></i>Top Sản phẩm doanh số cao</h6>
                                        <small class="text-muted">Tính trên đơn hoàn thành</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="switchView('products'); return false;">
                                        Xem SP <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-3">#</th>
                                                <th>Sản phẩm</th>
                                                <th class="text-center">Đã bán</th>
                                                <th class="text-end">Doanh thu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($topProducts)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu bán hàng</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($topProducts as $idx => $tp): ?>
                                                    <tr>
                                                        <td class="px-3 fw-bold text-muted"><?php echo $idx + 1; ?></td>
                                                        <td>
                                                            <div class="fw-semibold text-truncate" style="max-width: 170px;" title="<?php echo htmlspecialchars($tp['product_name'] ?? ''); ?>">
                                                                <?php echo htmlspecialchars($tp['product_name'] ?? '—'); ?>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-light text-dark border"><?php echo (int)($tp['total_sold'] ?? 0); ?></span>
                                                        </td>
                                                        <td class="text-end fw-bold text-success">
                                                            <?php echo number_format($tp['total_revenue'] ?? 0, 0, ',', '.') . 'đ'; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Box Section -->
                <div id="view-chats" class="view-section">
                    <div class="card-custom overflow-hidden" style="height: calc(100vh - 160px); min-height: 520px;">
                        <div class="row g-0 h-100">
                            <!-- Left Column: Conversations List -->
                            <div class="col-12 col-md-4 border-end h-100 d-flex flex-column bg-white" id="admin-chat-col-list">
                                <div class="p-3 border-bottom bg-light">
                                    <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-comments me-2"></i>Hội thoại Chat</h6>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="admin-chat-search" class="form-control" placeholder="Tìm theo tên/session..." onkeyup="AdminChat.filterConversations()">
                                    </div>
                                </div>
                                <div class="flex-grow-1 overflow-auto" id="admin-conversations-list">
                                    <div class="text-center p-4 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tải hội thoại...</div>
                                </div>
                            </div>

                            <!-- Right Column: Active Conversation Messages & Input -->
                            <div class="col-12 col-md-8 h-100 d-none d-md-flex flex-column bg-light" id="admin-chat-col-detail">
                                <div id="admin-chat-header" class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-light border d-md-none me-2 shadow-none" onclick="AdminChat.showMobileList()" title="Quay lại danh sách">
                                            <i class="fa-solid fa-arrow-left"></i>
                                        </button>
                                        <div class="avatar-circle bg-dark text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        <div>
                                            <h6 id="admin-chat-user-name" class="fw-bold mb-0 text-dark">Chọn một cuộc hội thoại</h6>
                                            <small id="admin-chat-user-sub" class="text-muted">Chưa có hội thoại nào được chọn</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-grow-1 p-3 overflow-auto" id="admin-messages-container" style="background: #f8fafc;">
                                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="fa-regular fa-comments fa-3x mb-3 opacity-50"></i>
                                        <p class="mb-0 fw-medium">Chọn một cuộc hội thoại ở danh sách bên trái để bắt đầu chat trực tiếp.</p>
                                    </div>
                                </div>

                                <div class="p-3 border-top bg-white" id="admin-chat-input-area" style="display: none;">
                                    <form id="admin-chat-form" onsubmit="AdminChat.sendMessage(event)">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-light border text-secondary px-3" onclick="document.getElementById('admin-chat-image-input').click()" title="Gửi hình ảnh">
                                                <i class="fa-regular fa-image" style="font-size: 1.15rem;"></i>
                                            </button>
                                            <input type="file" id="admin-chat-image-input" accept="image/png,image/jpeg,image/gif,image/webp" style="display:none;" onchange="AdminChat.handleImageUpload(event)">
                                            <input type="text" id="admin-chat-input" class="form-control" placeholder="Nhập tin nhắn phản hồi..." autocomplete="off">
                                            <button class="btn btn-dark px-4 fw-bold" type="submit">
                                                <i class="fa-solid fa-paper-plane me-1"></i> Gửi
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm Sản phẩm mới</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="productForm">
                        <input type="hidden" id="p_id">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Tên sản phẩm</label>
                                <input type="text" class="form-control" id="p_title" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Danh mục</label>
                                <select class="form-select" id="p_category" required>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Giá giảm / Giá bán (VNĐ)</label>
                                <input type="number" class="form-control" id="p_price" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Giá gốc (VNĐ)</label>
                                <input type="number" class="form-control" id="p_original_price" min="0" placeholder="Để trống nếu không giảm giá">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" id="p_status">
                                    <option value="active">Đang bán</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                    <option value="hidden">Ẩn</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <input type="hidden" id="p_image">
                                <label class="form-label">Hình ảnh sản phẩm</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="product-image-preview-wrap">
                                        <img src="" id="p_image_preview" class="product-image-preview d-none">
                                        <div class="product-image-placeholder" id="p_image_placeholder">
                                            <i class="fa-regular fa-image"></i>
                                            <span>Chưa có ảnh</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control form-control-sm" id="p_image_file" accept="image/png,image/jpeg,image/webp,image/gif">
                                        <small class="text-muted d-block mt-1">Định dạng: JPG, PNG, WEBP, GIF. Tối đa 10MB.</small>
                                        <button type="button" class="btn btn-sm btn-link text-danger px-0 mt-1 d-none" id="p_image_clear">
                                            <i class="fa-solid fa-xmark me-1"></i>Bỏ ảnh
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Mô tả ngắn</label>
                                <input type="text" class="form-control" id="p_desc"
                                    placeholder="Ví dụ: Cấp tốc 5 phút, Bảo hành trọn đời...">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">4 dòng hiển thị trên card sản phẩm</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control card-feature-input" id="p_card_feature_1" placeholder="VD: Gateway và Proxy">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control card-feature-input" id="p_card_feature_2" placeholder="VD: Trí thông minh 1:1">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control card-feature-input" id="p_card_feature_3" placeholder="VD: Nạp bao nhiêu dùng bấy nhiêu">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control card-feature-input" id="p_card_feature_4" placeholder="VD: Model Opus 4.7, Sonnet 4.6">
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">Chỉ hiển thị ở danh sách sản phẩm. Trang chi tiết không dùng 4 dòng này.</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Mô tả chi tiết / Nội dung bộ công cụ</label>
                                <div class="product-detail-toolbar btn-group flex-wrap mb-1" role="toolbar" aria-label="Công cụ định dạng mô tả sản phẩm">
                                    <button type="button" class="btn btn-sm btn-light border icon-only" data-cmd="bold" title="Đậm"><i class="fa-solid fa-bold"></i></button>
                                    <button type="button" class="btn btn-sm btn-light border icon-only" data-cmd="italic" title="Nghiêng"><i class="fa-solid fa-italic"></i></button>
                                    <button type="button" class="btn btn-sm btn-light border icon-only" data-cmd="insertUnorderedList" title="Danh sách"><i class="fa-solid fa-list-ul"></i></button>
                                    <button type="button" class="btn btn-sm btn-light border icon-only" data-cmd="insertOrderedList" title="Danh sách số"><i class="fa-solid fa-list-ol"></i></button>
                                    <button type="button" class="btn btn-sm btn-light border" data-cmd="formatBlock" data-arg="H2" title="Tiêu đề H2">H2</button>
                                    <button type="button" class="btn btn-sm btn-light border" data-cmd="formatBlock" data-arg="H3" title="Tiêu đề H3">H3</button>
                                    <button type="button" class="btn btn-sm btn-light border" data-template="table" title="Bảng gói">Bảng</button>
                                    <button type="button" class="btn btn-sm btn-light border" data-template="cta" title="CTA">CTA</button>
                                    <button type="button" class="btn btn-sm btn-light border" data-template="seo" title="Khung mô tả SEO">Mẫu SEO</button>
                                    <button type="button" class="btn btn-sm btn-light border icon-only text-danger" data-cmd="removeFormat" title="Xóa định dạng"><i class="fa-solid fa-eraser"></i></button>
                                </div>
                                <div id="p_detail_desc_editor" class="rich-editor product-detail-editor" contenteditable="true"
                                     data-placeholder="Dán nội dung từ ChatGPT, Word hoặc website vào đây. Editor sẽ giữ heading, bullet, bảng cơ bản."></div>
                                <textarea class="d-none" id="p_detail_desc"></textarea>
                            </div>

                            <div class="col-12 mb-3 border-top pt-3">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-magnifying-glass me-1"></i> Cấu hình SEO tối ưu Google</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">SEO Slug (Đường dẫn thân thiện)</label>
                                        <input type="text" class="form-control" id="p_seo_slug" placeholder="VD: mua-tai-khoan-chatgpt-plus">
                                        <small class="text-muted">Để trống để tự động tạo từ tên sản phẩm.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">SEO Title (Tiêu đề Google)</label>
                                        <input type="text" class="form-control" id="p_seo_title" placeholder="Từ 50-60 ký tự">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">SEO Description (Mô tả Google)</label>
                                        <textarea class="form-control" id="p_seo_description" rows="2" placeholder="Tóm tắt nội dung khi tìm kiếm trên Google (từ 150-160 ký tự)"></textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">SEO Keywords (Từ khóa Google)</label>
                                        <input type="text" class="form-control" id="p_seo_keywords" placeholder="Ví dụ: mua chatgpt, tai khoan gpt gia re (cách nhau bằng dấu phẩy)">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Các loại / Gói dịch vụ (Variants)</label>
                                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="addVariantRow()">
                                        <i class="fa-solid fa-plus me-1"></i> Thêm loại
                                    </button>
                                </div>
                                <div class="row g-2 mb-1 px-1 small text-muted fw-semibold d-none d-md-flex">
                                    <div class="col-md-3">Tên gói</div>
                                    <div class="col-md-2">Giá bán</div>
                                    <div class="col-md-2">Giá gốc</div>
                                    <div class="col-md-1">Kho</div>
                                    <div class="col-md-3 text-center">Up / MK / Kho</div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div id="variant-container" class="bg-light p-3 rounded-3 border">
                                    <!-- Variant rows will be added here -->
                                </div>
                                <small class="text-muted d-block mt-1">Để trống <strong>Giá gốc</strong> nếu không có khuyến mãi. Khi giá gốc &gt; giá bán, hệ thống tự hiển thị giá gạch ngang và badge giảm %.</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-black px-4" onclick="saveProduct()">Lưu dữ liệu</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- AppNotify system - Load from public_html root so admin uses the same deployed file -->
    <?php $mainJsVersion = is_file(public_path('assets/js/main.js')) ? filemtime(public_path('assets/js/main.js')) : time(); ?>
    <script src="/assets/js/main.js?v=<?php echo $mainJsVersion; ?>"></script>

    <?php
        $jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | (defined('JSON_INVALID_UTF8_SUBSTITUTE') ? JSON_INVALID_UTF8_SUBSTITUTE : 0);
        if (!function_exists('safeAdminJson')) {
            function safeAdminJson($data, $flags) {
                try {
                    $res = json_encode($data, $flags);
                    return ($res !== false && $res !== null) ? $res : '[]';
                } catch (\Throwable $t) {
                    return '[]';
                }
            }
        }
    ?>
    <script>
        const APP_STATE = {
            categories: <?php echo safeAdminJson($categories, $jsonFlags); ?>,
            settings: <?php echo safeAdminJson($settings, $jsonFlags); ?>,
            products: <?php echo safeAdminJson($products, $jsonFlags); ?>,
            orders: <?php echo safeAdminJson($orders, $jsonFlags); ?>,
            users: <?php echo safeAdminJson($users ?? [], $jsonFlags); ?>,
            blogs: <?php echo safeAdminJson($blogs ?? [], $jsonFlags); ?>,
            contactMessages: <?php echo safeAdminJson($contactMessages ?? [], $jsonFlags); ?>,
            monthlyStats: <?php echo safeAdminJson($monthlyStats ?? [], $jsonFlags); ?>,
            topProducts: <?php echo safeAdminJson($topProducts ?? [], $jsonFlags); ?>,
            recentOrders: <?php echo safeAdminJson($recentOrders ?? [], $jsonFlags); ?>,
            csrfToken: <?php echo safeAdminJson(Csrf::token(), $jsonFlags); ?>
        };

        let ordersCurrentPage = 1;
        let ordersTotalPages = <?php echo $ordersTotalPages ?? 1; ?>;
        let ordersTotalCount = <?php echo (int)($totalOrders ?? 0); ?>;
        let ordersSearchQuery = '';
        let ordersStatusFilter = '';

        let productsCurrentPage = 1;
        const productsPerPage = 10;
        let productFilterKeyword = '';
        let productFilterCategory = '';
        let productFilterStatus = '';

        let usersCurrentPage = 1;
        const usersPerPage = 10;
        let userFilterKeyword = '';
        let userFilterRole = '';
        let userFilterStatus = '';

        let blogsCurrentPage = 1;
        const blogsPerPage = 10;
        let blogFilterKeyword = '';

        let contactsCurrentPage = 1;
        const contactsPerPage = 10;
        let contactFilterKeyword = '';
        let contactFilterStatus = 'all';

        let categoryFilterKeyword = '';

        const FALLBACK_PRODUCT_IMAGE = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Crect width='60' height='60' fill='%23f1f5f9' rx='6'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='sans-serif' font-size='9' fill='%2394a3b8'%3ENO IMAGE%3C/text%3E%3C/svg%3E";

        function apiGet(action, params = {}) {
            let url = '?action=';
            if (typeof action === 'string') {
                if (action.startsWith('?') || action.startsWith('action=')) {
                    url = action.startsWith('?') ? action : ('?' + action);
                } else {
                    url = '?action=' + action;
                }
            }
            if (params && Object.keys(params).length > 0) {
                const sp = new URLSearchParams(params);
                url += (url.includes('?') ? '&' : '?') + sp.toString();
            }
            return fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            }).then(res => res.json());
        }

        function apiPost(action, formData) {
            if (!(formData instanceof FormData)) {
                const fd = new FormData();
                Object.entries(formData || {}).forEach(([k, v]) => fd.append(k, v));
                formData = fd;
            }
            formData.append('csrf_token', APP_STATE.csrfToken);
            return fetch('?action=' + action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: formData,
                credentials: 'same-origin'
            }).then(res => res.json());
        }

        function pushIndexAll() {
            AppNotify.info('Đang gửi toàn bộ URL public lên Google Indexing API...', 'Push index');
            apiPost('adminPushIndexAll', {})
                .then(data => {
                    if (data.success) {
                        AppNotify.success(`Đã gửi ${data.submitted || 0}/${data.total || 0} URL.`, 'Push index');
                    } else {
                        AppNotify.error(data.message || 'Không thể push index.', 'Lỗi indexing');
                    }
                })
                .catch(() => AppNotify.error('Không thể kết nối API push index.', 'Lỗi indexing'));
        }

        function addIndexUrlRow(value = '') {
            const wrap = document.getElementById('index-url-rows');
            if (!wrap) return;
            const row = document.createElement('div');
            row.className = 'input-group index-url-row';
            row.innerHTML = `
                <input type="url" class="form-control index-url-input" placeholder="https://aicuatoi.net/duong-dan-can-index" value="${String(value).replace(/"/g, '&quot;')}">
                <button class="btn btn-light border" type="button" onclick="removeIndexUrlRow(this)" title="Xóa hàng">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            wrap.appendChild(row);
        }

        function removeIndexUrlRow(btn) {
            const wrap = document.getElementById('index-url-rows');
            const rows = wrap ? wrap.querySelectorAll('.index-url-row') : [];
            if (rows.length <= 1) {
                const input = rows[0]?.querySelector('.index-url-input');
                if (input) input.value = '';
                return;
            }
            btn.closest('.index-url-row')?.remove();
        }

        function pushIndexUrls() {
            const urls = Array.from(document.querySelectorAll('.index-url-input'))
                .map(input => input.value.trim())
                .filter(Boolean);
            if (!urls.length) {
                AppNotify.warning('Nhập ít nhất 1 URL cần index.', 'Thiếu URL');
                return;
            }
            AppNotify.info('Đang gửi URL riêng lẻ lên Google Indexing API...', 'Push index');
            apiPost('adminPushIndexUrls', { urls: JSON.stringify(urls) })
                .then(data => {
                    if (data.success) {
                        AppNotify.success(`Đã gửi ${data.submitted || 0}/${data.total || 0} URL.`, 'Push index');
                    } else {
                        AppNotify.error(data.message || 'Không thể index URL.', 'Lỗi indexing');
                    }
                })
                .catch(() => AppNotify.error('Không thể kết nối API index URL.', 'Lỗi indexing'));
        }

        let productModal, categoryModal, blogModal, stockModal;
        function getProductModal() { return productModal ||= new bootstrap.Modal(document.getElementById('productModal')); }
        function getCategoryModal() { return categoryModal ||= new bootstrap.Modal(document.getElementById('categoryModal')); }
        function getBlogModal()    { return blogModal    ||= new bootstrap.Modal(document.getElementById('blogModal')); }
        function getStockModal()   { return stockModal   ||= new bootstrap.Modal(document.getElementById('stockModal')); }

        function slugifyVietnamese(text) {
            text = String(text);
            const map = [
                'á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ',
                'đ',
                'é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ',
                'í','ì','ỉ','ĩ','ị',
                'ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ',
                'ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự',
                'ý','ỳ','ỷ','ỹ','ỵ'
            ];
            const rep = [
                'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
                'd',
                'e','e','e','e','e','e','e','e','e','e','e',
                'i','i','i','i','i',
                'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
                'u','u','u','u','u','u','u','u','u','u','u',
                'y','y','y','y','y'
            ];
            text = text.toLowerCase();
            for (let i = 0; i < map.length; i++) {
                text = text.replace(new RegExp(map[i], 'g'), rep[i]);
            }
            return text.replace(/[^a-z0-9\-]+/g, '-')
                       .replace(/-+/g, '-')
                       .replace(/^-|-$/g, '');
        }

        document.addEventListener("DOMContentLoaded", () => {
            renderCategoriesSelect();
            renderCategoriesTable();
            renderProducts();
            renderOrders();
            renderUsers();
            renderBlogsTable();
            renderContacts();

            // Auto slugification listeners
            const pTitle = document.getElementById('p_title');
            const pSeoSlug = document.getElementById('p_seo_slug');
            if (pTitle && pSeoSlug) {
                pTitle.addEventListener('input', () => {
                    const id = document.getElementById('p_id').value;
                    if (!id && !pSeoSlug.dataset.manual) {
                        pSeoSlug.value = slugifyVietnamese(pTitle.value);
                    }
                });
                pSeoSlug.addEventListener('input', () => {
                    pSeoSlug.dataset.manual = 'true';
                });
            }

            const catName = document.getElementById('cat_name');
            const catSlug = document.getElementById('cat_slug');
            const catSeoSlug = document.getElementById('cat_seo_slug');
            if (catName && catSlug && catSeoSlug) {
                catName.addEventListener('input', () => {
                    const id = document.getElementById('cat_id').value;
                    if (!id) {
                        const slug = slugifyVietnamese(catName.value);
                        catSlug.value = slug;
                        if (!catSeoSlug.dataset.manual) {
                            catSeoSlug.value = slug;
                        }
                    }
                });
                catSeoSlug.addEventListener('input', () => {
                    catSeoSlug.dataset.manual = 'true';
                });
            }

            const blogTitle = document.getElementById('blog_title');
            const blogSeoSlug = document.getElementById('blog_seo_slug');
            if (blogTitle && blogSeoSlug) {
                blogTitle.addEventListener('input', () => {
                    const id = document.getElementById('blog_id').value;
                    if (!id && !blogSeoSlug.dataset.manual) {
                        blogSeoSlug.value = slugifyVietnamese(blogTitle.value);
                    }
                });
                blogSeoSlug.addEventListener('input', () => {
                    blogSeoSlug.dataset.manual = 'true';
                });
            }

            // Setup image pickers
            setupProductImagePicker();
            setupBlogModal();
            renderKeywords();

            // Restore active tab (URL param > Hash > localStorage > Default 'dashboard')
            const initialTab = getSavedAdminTab();
            switchView(initialTab, null, false);

            window.addEventListener('hashchange', () => {
                const hash = (window.location.hash || '').replace(/^#/, '');
                if (hash && document.getElementById('view-' + hash)) {
                    switchView(hash, null, false);
                }
            });
        });

        // ================= 6-MONTH DASHBOARD CHART =================
        let monthlyChartInstance = null;
        function renderDashboardChart() {
            const canvas = document.getElementById('dashboardMonthlyChart');
            if (!canvas || typeof Chart === 'undefined') return;

            const stats = APP_STATE.monthlyStats || [];
            if (!Array.isArray(stats) || stats.length === 0) return;

            const labels = stats.map(s => s.label || s.short_label || s.ym);
            const revenues = stats.map(s => Number(s.revenue || 0));
            const successfulOrders = stats.map(s => Number(s.successful_orders || 0));

            if (monthlyChartInstance) {
                try { monthlyChartInstance.destroy(); } catch(e) {}
            }

            const ctx = canvas.getContext('2d');
            const revGradient = ctx.createLinearGradient(0, 0, 0, 300);
            revGradient.addColorStop(0, 'rgba(17, 24, 39, 0.95)');
            revGradient.addColorStop(1, 'rgba(55, 65, 81, 0.45)');

            monthlyChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Doanh thu (VNĐ)',
                            data: revenues,
                            backgroundColor: revGradient,
                            borderRadius: 6,
                            borderSkipped: false,
                            yAxisID: 'yRevenue',
                            order: 2,
                            barPercentage: 0.52
                        },
                        {
                            label: 'Đơn thành công',
                            data: successfulOrders,
                            type: 'line',
                            borderColor: '#10b981',
                            backgroundColor: '#10b981',
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.35,
                            yAxisID: 'yOrders',
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { family: 'Inter', size: 12, weight: '600' },
                                padding: 16
                            }
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleFont: { family: 'Inter', size: 13, weight: '700' },
                            bodyFont: { family: 'Inter', size: 12 },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.yAxisID === 'yRevenue') {
                                        return ' Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                                    } else {
                                        return ' Đơn thành công: ' + context.parsed.y + ' đơn';
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 12 } }
                        },
                        yRevenue: {
                            type: 'linear',
                            position: 'left',
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                    return value;
                                }
                            },
                            title: {
                                display: true,
                                text: 'Doanh thu (VNĐ)',
                                font: { family: 'Inter', size: 11, weight: '600' }
                            }
                        },
                        yOrders: {
                            type: 'linear',
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                stepSize: 1,
                                font: { family: 'Inter', size: 11 }
                            },
                            title: {
                                display: true,
                                text: 'Số đơn',
                                font: { family: 'Inter', size: 11, weight: '600' }
                            }
                        }
                    }
                }
            });
        }

        // ================= SHARED PAGINATION CONTROLLER =================
        function renderPaginationControls(containerId, currentPage, totalPages, clickFnName) {
            const container = document.getElementById(containerId);
            if (!container) return;
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            // Previous button
            html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                <button class="page-link shadow-none" onclick="${clickFnName}(${currentPage - 1})" aria-label="Previous" ${currentPage <= 1 ? 'disabled' : ''}>
                    <i class="fa-solid fa-chevron-left small"></i>
                </button>
            </li>`;

            // Dynamic numeric buttons with ellipsis
            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            if (startPage > 1) {
                html += `<li class="page-item"><button class="page-link shadow-none" onclick="${clickFnName}(1)">1</button></li>`;
                if (startPage > 2) {
                    html += `<li class="page-item disabled"><span class="page-link shadow-none">...</span></li>`;
                }
            }

            for (let p = startPage; p <= endPage; p++) {
                html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                    <button class="page-link shadow-none" onclick="${clickFnName}(${p})">${p}</button>
                </li>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<li class="page-item disabled"><span class="page-link shadow-none">...</span></li>`;
                }
                html += `<li class="page-item"><button class="page-link shadow-none" onclick="${clickFnName}(${totalPages})">${totalPages}</button></li>`;
            }

            // Next button
            html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                <button class="page-link shadow-none" onclick="${clickFnName}(${currentPage + 1})" aria-label="Next" ${currentPage >= totalPages ? 'disabled' : ''}>
                    <i class="fa-solid fa-chevron-right small"></i>
                </button>
            </li>`;

            container.innerHTML = html;
        }

        // ================= GOOGLE INDEXING & SEO KEYWORDS JS =================
        let keywordsCurrentPage = 1;
        const keywordsPerPage = 20;
        let keywordsAllEntries = [];
        let keywordsAliasesByTarget = {};

        function renderKeywords() {
            const tbody = document.getElementById('keyword-table-body');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm me-2"></span>Đang tải dữ liệu...</td></tr>';

            fetch('?action=adminGetKeywords', { credentials: 'same-origin' })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Lỗi: ${data.message || 'Không thể tải từ khóa.'}</td></tr>`;
                        return;
                    }

                    APP_STATE.seoKeywords = data.keywords || {};
                    APP_STATE.seoAliases = data.aliases || {};

                    const keywords = data.keywords || {};
                    const aliases = data.aliases || {};

                    keywordsAliasesByTarget = {};
                    Object.entries(aliases).forEach(([alias, target]) => {
                        if (!keywordsAliasesByTarget[target]) {
                            keywordsAliasesByTarget[target] = [];
                        }
                        keywordsAliasesByTarget[target].push(alias);
                    });

                    keywordsAllEntries = Object.entries(keywords);
                    keywordsCurrentPage = 1;

                    renderKeywordsPage();
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Lỗi kết nối: ${err.message}</td></tr>`;
                });
        }

        function renderKeywordsPage() {
            const tbody = document.getElementById('keyword-table-body');
            if (!tbody) return;

            if (keywordsAllEntries.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Chưa có từ khóa nào.</td></tr>';
                updateKeywordsPaginationControls(0);
                return;
            }

            const totalPages = Math.ceil(keywordsAllEntries.length / keywordsPerPage);
            if (keywordsCurrentPage > totalPages) keywordsCurrentPage = totalPages;
            if (keywordsCurrentPage < 1) keywordsCurrentPage = 1;

            const start = (keywordsCurrentPage - 1) * keywordsPerPage;
            const end = start + keywordsPerPage;
            const pageEntries = keywordsAllEntries.slice(start, end);

            tbody.innerHTML = '';
            pageEntries.forEach(([slug, info]) => {
                const targetAliases = keywordsAliasesByTarget[slug] || [];
                const aliasesHtml = targetAliases.map(a => `<code class="bg-light border px-1.5 py-0.5 rounded text-dark smaller me-1 mb-1 d-inline-block">${escapeHtml(a)}</code>`).join('') || '<span class="text-muted smaller">—</span>';
                const descPreview = info.description ? (info.description.length > 50 ? info.description.substring(0, 50) + '...' : info.description) : '<span class="text-muted italic smaller">Tự động tạo</span>';
                
                const escSlug = escapeHtml(slug);
                const escDisplayName = escapeHtml(info.display_name || '');
                
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold"><code>${escSlug}</code></td>
                        <td>${escDisplayName}</td>
                        <td><span class="small text-muted" title="${escapeHtml(info.description || '')}">${escapeHtml(descPreview)}</span></td>
                        <td><div class="d-flex flex-wrap" style="max-width: 250px;">${aliasesHtml}</div></td>
                        <td class="text-end">
                            <button class="btn-action" onclick="editKeyword('${escSlug}')" title="Sửa"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="deleteKeyword('${escSlug}')" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });

            updateKeywordsPaginationControls(totalPages);
        }

        function changeKeywordsPage(dir) {
            keywordsCurrentPage += dir;
            renderKeywordsPage();
        }

        function updateKeywordsPaginationControls(totalPages) {
            const curPageEl = document.getElementById('keywords-current-page');
            const totalPagesEl = document.getElementById('keywords-total-pages');
            const btnPrev = document.getElementById('keywords-btn-prev');
            const btnNext = document.getElementById('keywords-btn-next');

            if (curPageEl) curPageEl.innerText = totalPages === 0 ? 0 : keywordsCurrentPage;
            if (totalPagesEl) totalPagesEl.innerText = totalPages;
            if (btnPrev) btnPrev.disabled = (keywordsCurrentPage <= 1 || totalPages === 0);
            if (btnNext) btnNext.disabled = (keywordsCurrentPage >= totalPages || totalPages === 0);
        }

        let keywordModal;
        function getKeywordModal() { return keywordModal ||= new bootstrap.Modal(document.getElementById('keywordModal')); }

        function openKeywordModal() {
            document.getElementById('keywordForm').reset();
            document.getElementById('kw_old_slug').value = '';
            document.getElementById('kw_slug').readOnly = false;
            document.getElementById('keywordModalTitle').innerText = 'Thêm từ khóa mới';
            getKeywordModal().show();
        }

        function editKeyword(slug) {
            const info = APP_STATE.seoKeywords[slug];
            if (!info) return;

            document.getElementById('kw_old_slug').value = slug;
            document.getElementById('kw_slug').value = slug;
            document.getElementById('kw_slug').readOnly = false;
            document.getElementById('kw_display_name').value = info.display_name || '';
            document.getElementById('kw_title').value = info.title || '';
            document.getElementById('kw_description').value = info.description || '';
            document.getElementById('kw_keywords').value = (info.keywords || []).join(', ');

            const targetAliases = [];
            Object.entries(APP_STATE.seoAliases).forEach(([alias, target]) => {
                if (target === slug) {
                    targetAliases.push(alias);
                }
            });
            document.getElementById('kw_aliases').value = targetAliases.join(', ');

            document.getElementById('keywordModalTitle').innerText = 'Chỉnh sửa từ khóa';
            getKeywordModal().show();
        }

        function saveKeyword() {
            const slug = document.getElementById('kw_slug').value.trim().toLowerCase();
            const oldSlug = document.getElementById('kw_old_slug').value.trim().toLowerCase();
            const displayName = document.getElementById('kw_display_name').value.trim();
            const title = document.getElementById('kw_title').value.trim();
            const description = document.getElementById('kw_description').value.trim();
            const keywords = document.getElementById('kw_keywords').value.trim();
            const aliases = document.getElementById('kw_aliases').value.trim();

            if (!slug || !displayName) {
                AppNotify.warning('Từ khóa và Tên hiển thị không được để trống.', 'Thiếu thông tin');
                return;
            }

            if (!/^[a-z0-9\-]+$/.test(slug)) {
                AppNotify.warning('Từ khóa slug chỉ chứa chữ thường không dấu, số và dấu gạch ngang.', 'Sai định dạng');
                return;
            }

            const fd = new FormData();
            fd.append('slug', slug);
            fd.append('old_slug', oldSlug);
            fd.append('display_name', displayName);
            fd.append('title', title);
            fd.append('description', description);
            fd.append('keywords', keywords);
            fd.append('aliases', aliases);
            fd.append('csrf_token', APP_STATE.csrfToken);

            fetch('?action=adminSaveKeyword', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: fd,
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    AppNotify.success('Lưu từ khóa thành công!');
                    getKeywordModal().hide();
                    renderKeywords();
                } else {
                    AppNotify.error(data.message || 'Không thể lưu từ khóa.', 'Lỗi');
                }
            })
            .catch(() => AppNotify.error('Không thể kết nối server.', 'Lỗi kết nối'));
        }

        function deleteKeyword(slug) {
            Swal.fire({
                title: 'Xóa từ khóa này?',
                text: "Toàn bộ cấu hình và các từ đồng nghĩa (aliases) sẽ bị xóa!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Xóa ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    const fd = new FormData();
                    fd.append('slug', slug);
                    fd.append('csrf_token', APP_STATE.csrfToken);

                    fetch('?action=adminDeleteKeyword', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                        body: fd,
                        credentials: 'same-origin'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            AppNotify.success('Đã xóa từ khóa!');
                            renderKeywords();
                        } else {
                            AppNotify.error(data.message || 'Không thể xóa.', 'Lỗi');
                        }
                    })
                    .catch(() => AppNotify.error('Không thể kết nối server.', 'Lỗi'));
                }
            });
        }

        let bulkKeywordModal;
        function getBulkKeywordModal() {
            return bulkKeywordModal ||= new bootstrap.Modal(document.getElementById('bulkKeywordModal'));
        }

        let bulkParsedKeywordsCache = [];

        function openBulkKeywordModal() {
            document.getElementById('bulk-keywords-input').value = '';
            document.getElementById('bulk-keywords-preview-table-body').innerHTML = '';
            document.getElementById('bulk-keywords-preview-area').classList.add('d-none');
            document.getElementById('btnBulkKeywordsConfirm').disabled = true;
            document.getElementById('bulk-keywords-replace-all').checked = false;
            bulkParsedKeywordsCache = [];
            getBulkKeywordModal().show();
        }

        function previewBulkKeywords() {
            const raw = document.getElementById('bulk-keywords-input').value;
            const lines = raw.split(/\r?\n/);
            const tbody = document.getElementById('bulk-keywords-preview-table-body');
            tbody.innerHTML = '';
            bulkParsedKeywordsCache = [];

            let count = 0;
            let hasErrors = false;
            let errorMessage = '';

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i].trim();
                if (line === '') continue;

                const parts = line.split('|');
                if (parts.length < 2) {
                    hasErrors = true;
                    errorMessage = `Dòng ${i + 1} không đúng định dạng (thiếu dấu | để ngăn cách).`;
                    break;
                }

                const slug = parts[0].trim().toLowerCase();
                const displayName = parts[1].trim();
                const aliasesRaw = parts[2] ? parts[2].trim() : '';

                if (!slug) {
                    hasErrors = true;
                    errorMessage = `Dòng ${i + 1} thiếu Từ khóa (slug).`;
                    break;
                }
                if (!displayName) {
                    hasErrors = true;
                    errorMessage = `Dòng ${i + 1} thiếu Tên hiển thị.`;
                    break;
                }

                if (!/^[a-z0-9\-]+$/.test(slug)) {
                    hasErrors = true;
                    errorMessage = `Dòng ${i + 1} có slug "${slug}" không hợp lệ (chỉ được chứa chữ thường không dấu, số và dấu gạch ngang).`;
                    break;
                }

                const aliases = aliasesRaw.split(',').map(a => a.trim()).filter(Boolean);
                const displaySlugWords = slug.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                const autoTitle = `Tài khoản ${displaySlugWords} giá rẻ - Mua bán ${displaySlugWords} tự động`;

                bulkParsedKeywordsCache.push({
                    slug: slug,
                    display_name: displayName,
                    title: '',
                    description: '',
                    keywords: '',
                    aliases: aliasesRaw
                });

                const escSlug = escapeHtml(slug);
                const escDisplayName = escapeHtml(displayName);
                const escTitle = escapeHtml(autoTitle);
                const escAliases = aliases.map(a => `<code class="bg-light border px-1 py-0.5 rounded text-dark smaller me-1 mb-1 d-inline-block">${escapeHtml(a)}</code>`).join('') || '<span class="text-muted smaller">—</span>';

                tbody.innerHTML += `
                    <tr>
                        <td><code>${escSlug}</code></td>
                        <td>${escDisplayName}</td>
                        <td><span class="small text-muted">${escTitle}</span></td>
                        <td><div class="d-flex flex-wrap">${escAliases}</div></td>
                    </tr>
                `;
                count++;
            }

            if (hasErrors) {
                document.getElementById('bulk-keywords-preview-area').classList.add('d-none');
                document.getElementById('btnBulkKeywordsConfirm').disabled = true;
                bulkParsedKeywordsCache = [];
                AppNotify.warning(errorMessage, 'Định dạng không hợp lệ');
                return;
            }

            if (count > 0) {
                document.getElementById('bulk-keywords-preview-area').classList.remove('d-none');
                document.getElementById('btnBulkKeywordsConfirm').disabled = false;
            } else {
                document.getElementById('bulk-keywords-preview-area').classList.add('d-none');
                document.getElementById('btnBulkKeywordsConfirm').disabled = true;
                AppNotify.warning('Vui lòng nhập ít nhất một dòng từ khóa.', 'Không có dữ liệu');
            }
        }

        function confirmBulkKeywords() {
            if (bulkParsedKeywordsCache.length === 0) {
                AppNotify.warning('Không có dữ liệu từ khóa hợp lệ để lưu.', 'Lỗi');
                return;
            }

            const replaceAll = document.getElementById('bulk-keywords-replace-all').checked;

            if (replaceAll) {
                Swal.fire({
                    title: 'Xóa sạch & Nhập mới?',
                    text: "Bạn đã chọn tùy chọn xóa sạch toàn bộ từ khóa hiện có trên hệ thống. Tất cả từ khóa cũ sẽ bị xóa vĩnh viễn và thay thế bằng danh sách mới này!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Đồng ý xóa & nhập mới',
                    cancelButtonText: 'Hủy bỏ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeBulkSave(true);
                    }
                });
            } else {
                executeBulkSave(false);
            }
        }

        function executeBulkSave(replaceAll) {
            const btn = document.getElementById('btnBulkKeywordsConfirm');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

            const fd = new FormData();
            fd.append('keywords_json', JSON.stringify(bulkParsedKeywordsCache));
            fd.append('replace_all', replaceAll ? '1' : '0');
            fd.append('csrf_token', APP_STATE.csrfToken);

            fetch('?action=adminSaveKeywordsBulk', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: fd,
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    AppNotify.success(`Đã thêm thành công ${data.count} từ khóa!`);
                    getBulkKeywordModal().hide();
                    renderKeywords();
                } else {
                    AppNotify.error(data.message || 'Không thể lưu từ khóa hàng loạt.', 'Lỗi');
                }
            })
            .catch(err => {
                AppNotify.error('Không thể kết nối server.', 'Lỗi kết nối');
                console.error(err);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        // ================= GOOGLE INDEXING API REALTIME RUNNER =================
        window.indexingAbort = false;
        window.indexingRunning = false;

        function logToIndexingTerminal(msg, level = 'info') {
            const terminal = document.getElementById('indexing-terminal');
            if (!terminal) return;
            const now = new Date().toTimeString().split(' ')[0];
            const div = document.createElement('div');
            div.style.marginBottom = '3px';
            div.style.lineHeight = '1.45';

            if (level === 'success') {
                div.innerHTML = `<span style="color: #6ee7b7;">[${now}]</span> <span style="color: #22c55e; font-weight: bold;">✔ OK:</span> <span style="color: #f1f5f9;">${msg}</span>`;
            } else if (level === 'error') {
                div.innerHTML = `<span style="color: #fca5a5;">[${now}]</span> <span style="color: #ef4444; font-weight: bold;">✖ ERR:</span> <span style="color: #fecaca;">${msg}</span>`;
            } else if (level === 'warn') {
                div.innerHTML = `<span style="color: #fde68a;">[${now}]</span> <span style="color: #f59e0b; font-weight: bold;">⚠ WARN:</span> <span style="color: #fef3c7;">${msg}</span>`;
            } else {
                div.innerHTML = `<span style="color: #71717a;">[${now}]</span> <span style="color: #38bdf8;">➜</span> <span style="color: #e4e4e7;">${msg}</span>`;
            }

            terminal.appendChild(div);
            terminal.scrollTop = terminal.scrollHeight;
        }

        function clearIndexingConsole() {
            const terminal = document.getElementById('indexing-terminal');
            if (terminal) {
                terminal.innerHTML = '<div class="text-secondary">// Console đã được xóa. Sẵn sàng nhận lệnh mới...</div>';
            }
            document.getElementById('console-stat-total').innerText = '0';
            document.getElementById('console-stat-processed').innerText = '0';
            document.getElementById('console-stat-success').innerText = '0';
            document.getElementById('console-stat-failed').innerText = '0';
            document.getElementById('indexing-progress-bar').style.width = '0%';
            document.getElementById('indexing-live-status').innerText = 'Sẵn sàng thực hiện yêu cầu index.';
        }

        function refreshIndexingCounts() {
            apiGet('adminGetIndexStatus')
                .then(data => {
                    if (data && data.success && data.counts) {
                        const c = data.counts;
                        const elProd = document.getElementById('idx-count-products');
                        const elCat = document.getElementById('idx-count-categories');
                        const elBlog = document.getElementById('idx-count-blogs');
                        const elKw = document.getElementById('idx-count-keywords');
                        const elAll = document.getElementById('idx-count-all');
                        if (elProd) elProd.innerText = `${c.products || 0} URL`;
                        if (elCat) elCat.innerText = `${c.categories || 0} URL`;
                        if (elBlog) elBlog.innerText = `${c.blogs || 0} URL`;
                        if (elKw) elKw.innerText = `${c.keywords || 0} URL`;
                        if (elAll) elAll.innerText = `${c.all || 0} URL`;
                    }
                })
                .catch(() => {});
        }

        function addManualUrl(url) {
            const inputs = document.querySelectorAll('.dashboard-index-url-input');
            for (let input of inputs) {
                if (input.value.trim() === '') {
                    input.value = url;
                    input.focus();
                    return;
                }
            }
            addDashboardIndexUrlRow(url);
        }

        function addDashboardIndexUrlRow(value = '') {
            const wrap = document.getElementById('dashboard-index-url-rows');
            if (!wrap) return;
            const row = document.createElement('div');
            row.className = 'input-group dashboard-index-url-row';
            row.innerHTML = `
                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-link"></i></span>
                <input type="url" class="form-control dashboard-index-url-input border-start-0" placeholder="https://aicuatoi.net/..." value="${String(value).replace(/"/g, '&quot;')}">
                <button class="btn btn-light border text-danger" type="button" onclick="removeDashboardIndexUrlRow(this)" title="Xóa hàng">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            `;
            wrap.appendChild(row);
            const input = row.querySelector('.dashboard-index-url-input');
            if (input && !value) input.focus();
        }

        function removeDashboardIndexUrlRow(btn) {
            const wrap = document.getElementById('dashboard-index-url-rows');
            const rows = wrap ? wrap.querySelectorAll('.dashboard-index-url-row') : [];
            if (rows.length <= 1) {
                const input = rows[0]?.querySelector('.dashboard-index-url-input');
                if (input) input.value = '';
                return;
            }
            btn.closest('.dashboard-index-url-row')?.remove();
        }

        function pasteMultipleUrlsPrompt() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Dán nhiều URL cần index',
                    html: '<textarea id="swal-bulk-urls" class="form-control font-monospace" rows="6" placeholder="https://aicuatoi.net/san-pham/...\nhttps://aicuatoi.net/danh-muc/...\n(Mỗi URL một dòng)"></textarea>',
                    showCancelButton: true,
                    confirmButtonText: 'Thêm vào danh sách',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#111827',
                    preConfirm: () => {
                        const val = document.getElementById('swal-bulk-urls')?.value || '';
                        return val.split(/\r?\n/).map(u => u.trim()).filter(Boolean);
                    }
                }).then(res => {
                    if (res.isConfirmed && res.value && res.value.length) {
                        res.value.forEach(url => addManualUrl(url));
                        AppNotify.success(`Đã thêm ${res.value.length} URL vào ô nhập.`, 'Index thủ công');
                    }
                });
            } else {
                const raw = prompt('Dán danh sách URL (cách nhau bởi dấu phẩy hoặc dòng mới):');
                if (raw) {
                    const list = raw.split(/[\r\n,]+/).map(u => u.trim()).filter(Boolean);
                    list.forEach(url => addManualUrl(url));
                }
            }
        }

        function pushDashboardIndexUrls() {
            if (window.indexingRunning) {
                AppNotify.warning('Một tác vụ index khác đang chạy. Vui lòng chờ!', 'Đang bận');
                return;
            }

            const inputs = document.querySelectorAll('.dashboard-index-url-input');
            const urls = Array.from(inputs)
                .map(input => input.value.trim())
                .filter(Boolean);

            if (!urls.length) {
                AppNotify.warning('Vui lòng nhập ít nhất 1 URL cần index.', 'Thiếu URL');
                return;
            }

            const btn = document.getElementById('btn-manual-submit');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang gửi...';
            }

            logToIndexingTerminal(`Bắt đầu gửi ${urls.length} URL thủ công lên Google...`, 'info');
            document.getElementById('console-stat-total').innerText = urls.length;
            document.getElementById('console-stat-processed').innerText = '0';
            document.getElementById('console-stat-success').innerText = '0';
            document.getElementById('console-stat-failed').innerText = '0';
            document.getElementById('indexing-progress-bar').style.width = '20%';

            apiPost('adminPushIndexUrls', { urls: JSON.stringify(urls) })
                .then(data => {
                    if (data && data.success) {
                        let successCount = data.submitted || 0;
                        let totalCount = data.total || urls.length;
                        let failedCount = totalCount - successCount;

                        document.getElementById('console-stat-processed').innerText = totalCount;
                        document.getElementById('console-stat-success').innerText = successCount;
                        document.getElementById('console-stat-failed').innerText = failedCount;
                        document.getElementById('indexing-progress-bar').style.width = '100%';

                        if (data.results) {
                            for (let [url, item] of Object.entries(data.results)) {
                                if (item.success) {
                                    logToIndexingTerminal(`${url} → 200 OK (${item.message || 'Google đã tiếp nhận'})`, 'success');
                                } else {
                                    logToIndexingTerminal(`${url} → Thất bại: ${item.message || 'Lỗi'}`, 'error');
                                }
                            }
                        }

                        logToIndexingTerminal(`Hoàn tất gửi thủ công! Thành công: ${successCount}/${totalCount}`, successCount > 0 ? 'success' : 'warn');
                        AppNotify.success(`Đã gửi thành công ${successCount}/${totalCount} URL lên Google!`, 'Index thành công');
                    } else {
                        logToIndexingTerminal(`Lỗi: ${data.message || 'Không thể gửi index'}`, 'error');
                        AppNotify.error(data.message || 'Không thể index URL.', 'Lỗi Indexing');
                    }
                })
                .catch(err => {
                    logToIndexingTerminal(`Lỗi kết nối máy chủ: ${err.message || 'Lỗi mạng'}`, 'error');
                    AppNotify.error('Không thể kết nối API index.', 'Lỗi mạng');
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                    }
                });
        }

        async function startBatchIndexing(type) {
            if (window.indexingRunning) {
                AppNotify.warning('Một tác vụ index đang diễn ra. Vui lòng dừng hoặc chờ hoàn tất!', 'Đang chạy');
                return;
            }

            window.indexingRunning = true;
            window.indexingAbort = false;

            const btnStop = document.getElementById('btn-stop-indexing');
            if (btnStop) btnStop.style.display = 'inline-block';

            document.querySelectorAll('.bulk-btn').forEach(b => b.disabled = true);
            const liveStatus = document.getElementById('indexing-live-status');
            if (liveStatus) liveStatus.innerText = 'Đang chuẩn bị danh sách URL...';

            logToIndexingTerminal(`Chuẩn bị nạp danh mục [${type.toUpperCase()}] từ hệ thống...`, 'info');

            try {
                const res = await apiGet(`adminGetIndexUrlsByType&type=${encodeURIComponent(type)}`);
                if (!res || !res.success || !res.urls || !res.urls.length) {
                    logToIndexingTerminal(`Không tìm thấy URL nào thuộc loại [${type}] để index.`, 'warn');
                    AppNotify.warning(`Không có URL nào cần index cho danh mục [${type}].`, 'Danh sách rỗng');
                    finishBatchIndexing();
                    return;
                }

                const urlItems = res.urls;
                const total = urlItems.length;
                let processed = 0;
                let success = 0;
                let failed = 0;

                document.getElementById('console-stat-total').innerText = total;
                document.getElementById('console-stat-processed').innerText = '0';
                document.getElementById('console-stat-success').innerText = '0';
                document.getElementById('console-stat-failed').innerText = '0';
                document.getElementById('indexing-progress-bar').style.width = '0%';

                logToIndexingTerminal(`Bắt đầu chạy batch runner: Tổng cộng ${total} URL [${type}]...`, 'info');
                if (liveStatus) liveStatus.innerText = `Đang gửi Google Indexing (0/${total})...`;

                // Send in micro-batches of 2 URLs to avoid blocking and give real-time animated feedback
                const batchSize = 2;
                for (let i = 0; i < urlItems.length; i += batchSize) {
                    if (window.indexingAbort) {
                        logToIndexingTerminal('Tác vụ index đã bị người dùng dừng lại!', 'warn');
                        AppNotify.info('Đã dừng tiến trình index.', 'Tạm dừng');
                        break;
                    }

                    const batch = urlItems.slice(i, i + batchSize);
                    const batchUrls = batch.map(b => b.url);

                    try {
                        const batchRes = await apiPost('adminPushIndexUrls', { urls: JSON.stringify(batchUrls) });
                        if (batchRes && batchRes.success && batchRes.results) {
                            for (let [url, result] of Object.entries(batchRes.results)) {
                                processed++;
                                if (result.success) {
                                    success++;
                                    logToIndexingTerminal(`${url} → 200 OK (${result.message || 'Tiếp nhận'})`, 'success');
                                } else {
                                    failed++;
                                    logToIndexingTerminal(`${url} → 400: ${result.message || 'Thất bại'}`, 'error');
                                }
                            }
                        } else {
                            processed += batch.length;
                            failed += batch.length;
                            batch.forEach(b => logToIndexingTerminal(`${b.url} → Thất bại: ${batchRes.message || 'Lỗi'}`, 'error'));
                        }
                    } catch (batchErr) {
                        processed += batch.length;
                        failed += batch.length;
                        batch.forEach(b => logToIndexingTerminal(`${b.url} → Lỗi mạng: ${batchErr.message || 'Timeout'}`, 'error'));
                    }

                    // Update live UI
                    const pct = Math.round((processed / total) * 100);
                    document.getElementById('console-stat-processed').innerText = processed;
                    document.getElementById('console-stat-success').innerText = success;
                    document.getElementById('console-stat-failed').innerText = failed;
                    document.getElementById('indexing-progress-bar').style.width = `${pct}%`;
                    if (liveStatus) liveStatus.innerText = `Đang xử lý ${processed}/${total} URL (${pct}%)...`;

                    // Brief breathing gap (80ms) for UI smoothness
                    await new Promise(r => setTimeout(r, 80));
                }

                if (!window.indexingAbort) {
                    logToIndexingTerminal(`Hoàn thành toàn bộ! Thành công: ${success}/${total} URL.`, success > 0 ? 'success' : 'warn');
                    AppNotify.success(`Đã gửi thành công ${success}/${total} URL lên Google!`, 'Hoàn tất Index');
                    if (liveStatus) liveStatus.innerText = `Hoàn tất: Đã gửi ${success}/${total} URL thành công.`;
                } else {
                    if (liveStatus) liveStatus.innerText = `Đã dừng: ${success}/${processed} URL thành công.`;
                }

            } catch (err) {
                logToIndexingTerminal(`Lỗi nghiêm trọng: ${err.message || 'Không thể tải danh sách URL'}`, 'error');
                AppNotify.error('Không thể thực hiện tác vụ index.', 'Lỗi');
            } finally {
                finishBatchIndexing();
            }
        }

        function stopBatchIndexing() {
            window.indexingAbort = true;
            logToIndexingTerminal('Đang gửi tín hiệu dừng...', 'warn');
        }

        function finishBatchIndexing() {
            window.indexingRunning = false;
            window.indexingAbort = false;
            const btnStop = document.getElementById('btn-stop-indexing');
            if (btnStop) btnStop.style.display = 'none';
            document.querySelectorAll('.bulk-btn').forEach(b => b.disabled = false);
        }

        function renderCategoriesSelect() {
            const select = document.getElementById('p_category');
            if (select) {
                select.innerHTML = (APP_STATE.categories || []).map(cat => `<option value="${cat.slug}">${cat.name}</option>`).join('');
            }
            const filterSelect = document.getElementById('product-filter-category');
            if (filterSelect) {
                let opts = '<option value="">Tất cả danh mục</option>';
                (APP_STATE.categories || []).forEach(cat => {
                    opts += `<option value="${cat.slug}">${cat.name}</option>`;
                });
                filterSelect.innerHTML = opts;
            }
        }

        function handleCategorySearch() {
            categoryFilterKeyword = (document.getElementById('category-search-input')?.value || '').toLowerCase().trim();
            renderCategoriesTable();
        }

        function renderCategoriesTable() {
            const tbody = document.getElementById('category-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            let cats = APP_STATE.categories || [];
            if (categoryFilterKeyword) {
                cats = cats.filter(c => (c.name || '').toLowerCase().includes(categoryFilterKeyword) || (c.slug || '').toLowerCase().includes(categoryFilterKeyword));
            }

            if (cats.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="table-empty-state"><i class="fa-solid fa-folder-open"></i><div>Không tìm thấy danh mục phù hợp.</div></td></tr>`;
                return;
            }

            cats.forEach(cat => {
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold text-dark">${escapeHtml(cat.name)}</td>
                        <td><code>${escapeHtml(cat.slug)}</code></td>
                        <td>${cat.is_pro ? '<span class="badge bg-primary rounded-pill">Có (Glow)</span>' : '<span class="text-muted">Không</span>'}</td>
                        <td>${cat.icon ? `<i class="fa-solid ${escapeHtml(cat.icon)} ${escapeHtml(cat.icon_color || '')}"></i>` : '-'}</td>
                        <td class="text-end">
                            <button class="btn-action" onclick="editCategory(${cat.id})" title="Sửa"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="deleteCategory(${cat.id})" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }

        function getCategoryName(id) {
            const cat = (APP_STATE.categories || []).find(c => c.id === id || c.slug === id);
            return cat ? cat.name : id;
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, ch => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[ch]));
        }

        function handleUserSearch() {
            userFilterKeyword = (document.getElementById('user-search-input')?.value || '').toLowerCase().trim();
            usersCurrentPage = 1;
            renderUsers();
        }

        function handleUserFilter() {
            userFilterRole = document.getElementById('user-filter-role')?.value || '';
            userFilterStatus = document.getElementById('user-filter-status')?.value || '';
            usersCurrentPage = 1;
            renderUsers();
        }

        function resetUserFilter() {
            userFilterKeyword = '';
            userFilterRole = '';
            userFilterStatus = '';
            const sInput = document.getElementById('user-search-input');
            const rSelect = document.getElementById('user-filter-role');
            const stSelect = document.getElementById('user-filter-status');
            if (sInput) sInput.value = '';
            if (rSelect) rSelect.value = '';
            if (stSelect) stSelect.value = '';
            usersCurrentPage = 1;
            renderUsers();
        }

        function changeUsersPage(page) {
            usersCurrentPage = page;
            renderUsers();
        }

        function renderUsers() {
            const tbody = document.getElementById('user-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            let users = APP_STATE.users || [];

            if (userFilterKeyword) {
                users = users.filter(u => 
                    (u.name || '').toLowerCase().includes(userFilterKeyword) || 
                    (u.email || '').toLowerCase().includes(userFilterKeyword)
                );
            }
            if (userFilterRole) {
                users = users.filter(u => (u.role || 'user') === userFilterRole);
            }
            if (userFilterStatus) {
                users = users.filter(u => (u.status || 'active') === userFilterStatus);
            }

            const total = users.length;
            const totalPages = Math.max(1, Math.ceil(total / usersPerPage));
            if (usersCurrentPage > totalPages) usersCurrentPage = totalPages;

            const startIdx = (usersCurrentPage - 1) * usersPerPage;
            const endIdx = Math.min(startIdx + usersPerPage, total);
            const pagedUsers = users.slice(startIdx, endIdx);

            const countStart = document.getElementById('user-count-start');
            const countEnd = document.getElementById('user-count-end');
            const countTotal = document.getElementById('user-count-total');
            if (countStart) countStart.innerText = total === 0 ? 0 : startIdx + 1;
            if (countEnd) countEnd.innerText = endIdx;
            if (countTotal) countTotal.innerText = total;

            renderPaginationControls('user-pagination-container', usersCurrentPage, totalPages, 'changeUsersPage');

            if (pagedUsers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="table-empty-state"><i class="fa-solid fa-users-slash"></i><div>Không tìm thấy người dùng nào.</div></td></tr>';
                return;
            }

            pagedUsers.forEach(user => {
                const isActive = (user.status || 'active') === 'active';
                const statusBadge = isActive
                    ? '<span class="badge bg-success rounded-pill">Active</span>'
                    : '<span class="badge bg-danger rounded-pill">Blocked</span>';
                const roleBadge = (user.role || 'user') === 'admin'
                    ? '<span class="badge bg-dark rounded-pill">Admin</span>'
                    : '<span class="badge bg-secondary rounded-pill">User</span>';
                const createdAt = user.created_at ? new Date(String(user.created_at).replace(' ', 'T')).toLocaleString('vi-VN') : '';
                const toggleLabel = isActive ? 'Block' : 'Mở block';
                const toggleIcon = isActive ? 'fa-ban' : 'fa-unlock';
                const toggleClass = isActive ? 'btn-outline-danger' : 'btn-outline-success';

                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">${escapeHtml(user.name)}</div>
                            <div class="text-muted small">${escapeHtml(user.email)}</div>
                        </td>
                        <td>${roleBadge}</td>
                        <td>${statusBadge}</td>
                        <td class="text-muted small">${createdAt}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-dark me-1" onclick="openUserModal(${Number(user.id)})" title="Sửa"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-sm ${toggleClass} me-1" onclick="toggleUserStatus(${Number(user.id)})" title="${toggleLabel}"><i class="fa-solid ${toggleIcon}"></i></button>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="resetUserPassword(${Number(user.id)})" title="Reset mật khẩu"><i class="fa-solid fa-key"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${Number(user.id)})" title="Xoá user"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }

        function openUserModal(id) {
            const user = (APP_STATE.users || []).find(u => Number(u.id) === Number(id));
            if (!user) return;
            document.getElementById('user_id').value = user.id;
            document.getElementById('user_name').value = user.name || '';
            document.getElementById('user_email').value = user.email || '';
            document.getElementById('user_role').value = user.role || 'user';
            document.getElementById('user_status').value = user.status || 'active';
            new bootstrap.Modal(document.getElementById('userModal')).show();
        }

        function saveUser() {
            const fd = new FormData();
            fd.append('id', document.getElementById('user_id').value);
            fd.append('name', document.getElementById('user_name').value);
            fd.append('email', document.getElementById('user_email').value);
            fd.append('role', document.getElementById('user_role').value);
            fd.append('status', document.getElementById('user_status').value);

            apiPost('adminSaveUser', fd).then(data => {
                if (!data.success) {
                    AppNotify.error(data.message || 'Không thể lưu user.');
                    return;
                }
                const idx = APP_STATE.users.findIndex(u => Number(u.id) === Number(data.user.id));
                if (idx >= 0) APP_STATE.users[idx] = data.user;
                renderUsers();
                bootstrap.Modal.getInstance(document.getElementById('userModal'))?.hide();
                AppNotify.success('Đã lưu user.');
            }).catch(() => AppNotify.error('Không thể kết nối server.'));
        }

        function toggleUserStatus(id) {
            const user = (APP_STATE.users || []).find(u => Number(u.id) === Number(id));
            if (!user) return;
            const willBlock = (user.status || 'active') === 'active';
            Swal.fire({
                title: willBlock ? 'Block user này?' : 'Mở block user này?',
                text: user.email || '',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: willBlock ? '#dc3545' : '#198754',
                confirmButtonText: willBlock ? 'Block' : 'Mở block'
            }).then(result => {
                if (!result.isConfirmed) return;
                apiPost('adminToggleUserStatus', { id }).then(data => {
                    if (!data.success) {
                        AppNotify.error(data.message || 'Không thể cập nhật trạng thái.');
                        return;
                    }
                    user.status = data.status;
                    renderUsers();
                    AppNotify.success('Đã cập nhật trạng thái user.');
                }).catch(() => AppNotify.error('Không thể kết nối server.'));
            });
        }

        function resetUserPassword(id) {
            const user = (APP_STATE.users || []).find(u => Number(u.id) === Number(id));
            if (!user) return;
            Swal.fire({
                title: 'Reset mật khẩu user?',
                text: user.email || '',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#111',
                confirmButtonText: 'Reset'
            }).then(result => {
                if (!result.isConfirmed) return;
                apiPost('adminResetUserPassword', { id }).then(data => {
                    if (!data.success) {
                        AppNotify.error(data.message || 'Không thể reset mật khẩu.');
                        return;
                    }
                    document.getElementById('reset_pass_email').innerText = data.email || '';
                    document.getElementById('reset_pass_value').value = data.password || '';
                    new bootstrap.Modal(document.getElementById('resetPassModal')).show();
                }).catch(() => AppNotify.error('Không thể kết nối server.'));
            });
        }

        function deleteUser(id) {
            const user = (APP_STATE.users || []).find(u => Number(u.id) === Number(id));
            if (!user) return;
            Swal.fire({
                title: 'Xóa vĩnh viễn user này?',
                text: `${user.name} (${user.email})`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Xóa vĩnh viễn'
            }).then(result => {
                if (!result.isConfirmed) return;
                apiPost('adminDeleteUser', { id }).then(data => {
                    if (!data.success) {
                        AppNotify.error(data.message || 'Không thể xóa user.');
                        return;
                    }
                    APP_STATE.users = (APP_STATE.users || []).filter(u => Number(u.id) !== Number(id));
                    renderUsers();
                    AppNotify.success('Đã xóa user.');
                }).catch(() => AppNotify.error('Không thể kết nối server.'));
            });
        }

        function deleteBlockedUsers() {
            const blockedCount = (APP_STATE.users || []).filter(u => u.status === 'blocked').length;
            if (blockedCount === 0) {
                AppNotify.info('Không có tài khoản bị block nào để dọn dẹp.');
                return;
            }
            Swal.fire({
                title: `Xóa tất cả ${blockedCount} user bị Block?`,
                text: 'Hành động này sẽ xóa vĩnh viễn các tài khoản spam đã bị block.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Dọn dẹp ngay'
            }).then(result => {
                if (!result.isConfirmed) return;
                apiPost('adminDeleteBlockedUsers', {}).then(data => {
                    if (!data.success) {
                        AppNotify.error(data.message || 'Không thể xóa.');
                        return;
                    }
                    APP_STATE.users = (APP_STATE.users || []).filter(u => u.status !== 'blocked');
                    renderUsers();
                    AppNotify.success('Đã dọn dẹp tất cả user bị block.');
                }).catch(() => AppNotify.error('Không thể kết nối server.'));
            });
        }

        function loadSecurityLogs(fullLoad = true) {
            const url = fullLoad ? '?action=adminGetSecurityLogs' : '?action=adminGetSecurityLogs&only_active=1';
            fetch(url)
                .then(r => r.json())
                .then(res => {
                    if (!res.success) return;
                    renderActiveSessions(res.active_sessions || []);
                    if (fullLoad) {
                        renderBannedIps(res.banned_ips || []);
                        renderSecurityLogs(res.logs || []);
                    }
                }).catch(err => console.error(err));
        }

        function loadActiveSessionsOnly() {
            loadSecurityLogs(false);
        }

        function renderActiveSessions(sessions) {
            const tbody = document.getElementById('active-sessions-table-body');
            const badge = document.getElementById('active-sessions-count-badge');
            if (badge) badge.innerText = `${sessions.length} Đang Online`;
            if (!tbody) return;
            tbody.innerHTML = '';

            if (!sessions || sessions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Không có người dùng nào đang Online.</td></tr>';
                return;
            }

            sessions.forEach(sess => {
                const isUser = sess.is_logged_in;
                const userBadge = isUser
                    ? `<span class="badge bg-primary rounded-pill"><i class="fa-solid fa-user me-1"></i>${escapeHtml(sess.user_info)}</span>`
                    : `<span class="badge bg-secondary rounded-pill"><i class="fa-solid fa-user-secret me-1"></i>Khách vãng lai</span>`;
                
                const timeAgo = sess.seconds_ago <= 5 ? 'Vừa xong' : `${sess.seconds_ago} giây trước`;

                tbody.innerHTML += `
                    <tr class="align-middle">
                        <td>
                            ${userBadge}
                            <div class="text-muted extra-small mt-1">Session: <code>${escapeHtml(sess.session_id.substring(0, 16))}...</code></div>
                        </td>
                        <td class="font-monospace small">${escapeHtml(sess.ip)}</td>
                        <td><code class="text-dark bg-light p-1 rounded small fw-bold">${escapeHtml(sess.current_url || '/')}</code></td>
                        <td class="small text-muted"><i class="fa-regular fa-clock me-1"></i>${timeAgo}</td>
                        <td><span class="badge bg-success rounded-pill"><i class="fa-solid fa-circle me-1 small"></i>ONLINE</span></td>
                    </tr>
                `;
            });
        }

        function renderBannedIps(bannedList) {
            const tbody = document.getElementById('banned-ips-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            if (!bannedList || bannedList.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3"><i class="fa-solid fa-circle-check text-success me-1"></i> Cơ chế chặn / khóa IP đã được tắt.</td></tr>';
                return;
            }

            bannedList.forEach(item => {
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold text-danger"><i class="fa-solid fa-ban me-1"></i>${escapeHtml(item.ip)}</td>
                        <td><span class="badge bg-danger rounded-pill">${escapeHtml(item.reason || 'Dò quét / Tấn công')}</span></td>
                        <td><code class="small text-dark bg-light p-1 rounded">${escapeHtml(item.payload || item.url || '')}</code></td>
                        <td class="small text-muted">${escapeHtml(item.banned_at || '')}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-success" onclick="unbanIp('${escapeHtml(item.ip)}')">
                                <i class="fa-solid fa-key me-1"></i> Gỡ Block IP
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        window.SECURITY_LOGS_STATE = window.SECURITY_LOGS_STATE || {
            allLogs: [],
            currentPage: 1,
            pageSize: 10
        };

        function renderSecurityLogs(logs) {
            if (logs !== undefined) {
                window.SECURITY_LOGS_STATE.allLogs = logs || [];
            }

            const tbody = document.getElementById('security-logs-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            const allLogs = window.SECURITY_LOGS_STATE.allLogs || [];
            const totalLogs = allLogs.length;

            if (totalLogs === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3"><i class="fa-solid fa-info-circle text-primary me-1"></i> Ghi nhận log thao tác đã được tắt theo yêu cầu.</td></tr>';
                const elStart = document.getElementById('security-logs-count-start');
                const elEnd = document.getElementById('security-logs-count-end');
                const elTotal = document.getElementById('security-logs-count-total');
                const elPag = document.getElementById('security-logs-pagination-container');
                if (elStart) elStart.innerText = '0';
                if (elEnd) elEnd.innerText = '0';
                if (elTotal) elTotal.innerText = '0';
                if (elPag) elPag.innerHTML = '';
                return;
            }

            const totalPages = Math.max(1, Math.ceil(totalLogs / window.SECURITY_LOGS_STATE.pageSize));
            if (window.SECURITY_LOGS_STATE.currentPage > totalPages) {
                window.SECURITY_LOGS_STATE.currentPage = totalPages;
            }

            const startIdx = (window.SECURITY_LOGS_STATE.currentPage - 1) * window.SECURITY_LOGS_STATE.pageSize;
            const endIdx = Math.min(startIdx + window.SECURITY_LOGS_STATE.pageSize, totalLogs);
            const pageLogs = allLogs.slice(startIdx, endIdx);

            const elStart = document.getElementById('security-logs-count-start');
            const elEnd = document.getElementById('security-logs-count-end');
            const elTotal = document.getElementById('security-logs-count-total');
            if (elStart) elStart.innerText = (startIdx + 1).toString();
            if (elEnd) elEnd.innerText = endIdx.toString();
            if (elTotal) elTotal.innerText = totalLogs.toString();

            pageLogs.forEach(log => {
                const isSusp = log.is_suspicious == 1;
                const badge = isSusp 
                    ? '<span class="badge bg-danger rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i>Nghi ngờ</span>'
                    : '<span class="badge bg-light text-dark border rounded-pill">Bình thường</span>';
                
                tbody.innerHTML += `
                    <tr class="${isSusp ? 'table-danger' : ''}">
                        <td class="small text-muted">${escapeHtml(log.timestamp || log.created_at || '')}</td>
                        <td>
                            <div class="fw-bold small text-dark">${escapeHtml(log.user || log.user_info || 'Khách vãng lai')}</div>
                            <div class="text-muted extra-small">ID: ${escapeHtml(log.session_id ? log.session_id.substring(0,16)+'...' : '')}</div>
                        </td>
                        <td><span class="badge bg-secondary rounded-pill">${escapeHtml(log.action_type || 'ACCESS')}</span></td>
                        <td>
                            <div class="fw-bold small">${escapeHtml(log.url || '')}</div>
                            ${log.details ? `<div class="small text-muted">${escapeHtml(log.details)}</div>` : ''}
                        </td>
                        <td class="small font-monospace">${escapeHtml(log.ip || '')}</td>
                        <td>${badge}</td>
                    </tr>
                `;
            });

            renderSecurityLogsPagination(totalPages);
        }

        function renderSecurityLogsPagination(totalPages) {
            const container = document.getElementById('security-logs-pagination-container');
            if (!container) return;
            container.innerHTML = '';

            const curPage = window.SECURITY_LOGS_STATE.currentPage;

            // Prev Button
            const prevDisabled = curPage <= 1 ? 'disabled' : '';
            container.innerHTML += `
                <li class="page-item ${prevDisabled}">
                    <button class="btn btn-sm btn-outline-dark me-1 px-2 py-1" onclick="changeSecurityLogsPage(${curPage - 1})" ${prevDisabled}>
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                </li>
            `;

            let startPage = Math.max(1, curPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let p = startPage; p <= endPage; p++) {
                const activeClass = p === curPage ? 'btn-dark text-white fw-bold' : 'btn-outline-secondary';
                container.innerHTML += `
                    <li class="page-item me-1">
                        <button class="btn btn-sm ${activeClass} px-3 py-1" onclick="changeSecurityLogsPage(${p})">${p}</button>
                    </li>
                `;
            }

            // Next Button
            const nextDisabled = curPage >= totalPages ? 'disabled' : '';
            container.innerHTML += `
                <li class="page-item ${nextDisabled}">
                    <button class="btn btn-sm btn-outline-dark px-2 py-1" onclick="changeSecurityLogsPage(${curPage + 1})" ${nextDisabled}>
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </li>
            `;
        }

        function changeSecurityLogsPage(newPage) {
            window.SECURITY_LOGS_STATE.currentPage = newPage;
            renderSecurityLogs();
        }

        function unbanIp(ip) {
            Swal.fire({
                title: 'Mở khóa IP này?',
                text: `Cho phép IP ${ip} truy cập lại website?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Gỡ Block'
            }).then(res => {
                if (!res.isConfirmed) return;
                const fd = new FormData();
                fd.append('ip', ip);
                apiPost('adminUnbanIp', fd).then(data => {
                    if (data.success) {
                        AppNotify.success(data.message || 'Đã mở khóa IP.');
                        loadSecurityLogs();
                    } else {
                        AppNotify.error(data.message || 'Không thể gỡ block.');
                    }
                });
            });
        }

        function clearSecurityHistoryLogs() {
            Swal.fire({
                title: 'Xóa sạch Lịch sử Log?',
                text: 'Hành động này sẽ dọn dẹp toàn bộ log lịch sử thao tác cũ.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Xóa Sạch'
            }).then(res => {
                if (!res.isConfirmed) return;
                apiPost('adminClearSecurityLogs', new FormData()).then(data => {
                    if (data.success) {
                        AppNotify.success(data.message || 'Đã xóa toàn bộ lịch sử log.');
                        window.SECURITY_LOGS_STATE.allLogs = [];
                        window.SECURITY_LOGS_STATE.currentPage = 1;
                        loadSecurityLogs(true);
                    } else {
                        AppNotify.error(data.message || 'Không thể xóa log.');
                    }
                });
            });
        }

        function copyResetPassword() {
            const input = document.getElementById('reset_pass_value');
            input.select();
            input.setSelectionRange(0, input.value.length);
            navigator.clipboard?.writeText(input.value).then(() => {
                AppNotify.success('Đã copy mật khẩu.');
            }).catch(() => {
                document.execCommand('copy');
                AppNotify.success('Đã copy mật khẩu.');
            });
        }

        function formatCurrency(amount) {
            const num = Number(amount) || 0;
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num);
        }

        function handleProductSearch() {
            productFilterKeyword = (document.getElementById('product-search-input')?.value || '').toLowerCase().trim();
            productsCurrentPage = 1;
            renderProducts();
        }

        function handleProductFilter() {
            productFilterCategory = document.getElementById('product-filter-category')?.value || '';
            productFilterStatus = document.getElementById('product-filter-status')?.value || '';
            productsCurrentPage = 1;
            renderProducts();
        }

        function resetProductFilter() {
            productFilterKeyword = '';
            productFilterCategory = '';
            productFilterStatus = '';
            const sInput = document.getElementById('product-search-input');
            const cSelect = document.getElementById('product-filter-category');
            const stSelect = document.getElementById('product-filter-status');
            if (sInput) sInput.value = '';
            if (cSelect) cSelect.value = '';
            if (stSelect) stSelect.value = '';
            productsCurrentPage = 1;
            renderProducts();
        }

        function changeProductsPage(page) {
            productsCurrentPage = page;
            renderProducts();
        }

        function renderProducts() {
            const tbody = document.getElementById('product-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            let prods = APP_STATE.products || [];

            if (productFilterKeyword) {
                prods = prods.filter(p => 
                    (p.title || '').toLowerCase().includes(productFilterKeyword) ||
                    (p.description || '').toLowerCase().includes(productFilterKeyword) ||
                    (p.feature_text || '').toLowerCase().includes(productFilterKeyword)
                );
            }
            if (productFilterCategory) {
                prods = prods.filter(p => (p.category_slug || p.category) === productFilterCategory);
            }
            if (productFilterStatus) {
                prods = prods.filter(p => p.status === productFilterStatus);
            }

            const total = prods.length;
            const totalPages = Math.max(1, Math.ceil(total / productsPerPage));
            if (productsCurrentPage > totalPages) productsCurrentPage = totalPages;

            const startIdx = (productsCurrentPage - 1) * productsPerPage;
            const endIdx = Math.min(startIdx + productsPerPage, total);
            const pagedProducts = prods.slice(startIdx, endIdx);

            const countStart = document.getElementById('product-count-start');
            const countEnd = document.getElementById('product-count-end');
            const countTotal = document.getElementById('product-count-total');
            if (countStart) countStart.innerText = total === 0 ? 0 : startIdx + 1;
            if (countEnd) countEnd.innerText = endIdx;
            if (countTotal) countTotal.innerText = total;

            renderPaginationControls('product-pagination-container', productsCurrentPage, totalPages, 'changeProductsPage');

            if (pagedProducts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="table-empty-state"><i class="fa-solid fa-box-open"></i><div>Không tìm thấy sản phẩm nào phù hợp.</div></td></tr>';
                return;
            }

            pagedProducts.forEach(p => {
                let badgeClass = p.status === 'active' ? 'bg-success' : (p.status === 'out_of_stock' ? 'bg-warning text-dark' : 'bg-secondary');
                let statusText = p.status === 'active' ? 'Đang bán' : (p.status === 'out_of_stock' ? 'Hết hàng' : 'Đã ẩn');

                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${escapeHtml(p.image || '')}" class="img-thumbnail-custom me-3" alt="${escapeHtml(p.title)}" onerror="this.onerror=null; this.src=FALLBACK_PRODUCT_IMAGE;">
                                <div>
                                    <div class="fw-bold text-dark">${escapeHtml(p.title)}</div>
                                    <div class="text-muted small">${escapeHtml(p.feature_text || p.description)}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border px-2 py-1">${escapeHtml(getCategoryName(p.category_slug || p.category))}</span></td>
                        <td class="fw-bold text-dark">${formatCurrency(p.price)}</td>
                        <td><span class="badge ${badgeClass} rounded-pill">${statusText}</span></td>
                        <td class="text-end">
                            <button class="btn-action" onclick="editProduct('${p.id}')" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn-action delete" onclick="deleteProduct('${p.id}')" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            const dashTotal = document.getElementById('dash-total-products');
            if (dashTotal) dashTotal.innerText = (APP_STATE.products || []).length;
        }

        function setProductImagePreview(url) {
            const img = document.getElementById('p_image_preview');
            const placeholder = document.getElementById('p_image_placeholder');
            const clearBtn = document.getElementById('p_image_clear');
            if (!img || !placeholder || !clearBtn) return;
            if (url) {
                img.src = url;
                img.classList.remove('d-none');
                placeholder.classList.add('d-none');
                clearBtn.classList.remove('d-none');
            } else {
                img.src = '';
                img.classList.add('d-none');
                placeholder.classList.remove('d-none');
                clearBtn.classList.add('d-none');
            }
        }

        function setupProductImagePicker() {
            const fileInput = document.getElementById('p_image_file');
            const clearBtn  = document.getElementById('p_image_clear');
            if (!fileInput || !clearBtn) return;

            fileInput.addEventListener('change', () => {
                const f = fileInput.files[0];
                if (!f) return;
                if (f.size > 10 * 1024 * 1024) {
                    AppNotify.warning('Tối đa 10MB mỗi tệp.', 'Tệp quá lớn');
                    fileInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => setProductImagePreview(e.target.result);
                reader.readAsDataURL(f);
            });

            clearBtn.addEventListener('click', () => {
                fileInput.value = '';
                document.getElementById('p_image').value = '';
                setProductImagePreview('');
            });
        }

        function openProductModal() {
            document.getElementById('productForm').reset();
            document.getElementById('p_id').value = '';
            document.getElementById('p_image').value = '';
            document.getElementById('p_original_price').value = '';
            setProductImagePreview('');
            document.querySelectorAll('.card-feature-input').forEach(input => input.value = '');
            document.getElementById('p_detail_desc').value = '';
            document.getElementById('p_detail_desc_editor').innerHTML = '';
            document.getElementById('p_seo_slug').value = '';
            document.getElementById('p_seo_title').value = '';
            document.getElementById('p_seo_description').value = '';
            document.getElementById('p_seo_keywords').value = '';
            document.getElementById('modalTitle').innerText = "Thêm Sản phẩm mới";
            renderVariants([]);
            getProductModal().show();
        }

        function editProduct(id) {
            const p = APP_STATE.products.find(item => item.id == id);
            if (p) {
                document.getElementById('p_id').value = p.id;
                document.getElementById('p_title').value = p.title;

                // Chọn danh mục thông minh
                const catSelect = document.getElementById('p_category');
                const targetCat = (p.category_slug || p.category || '').toLowerCase();

                let found = false;
                for (let i = 0; i < catSelect.options.length; i++) {
                    const opt = catSelect.options[i];
                    if (opt.value.toLowerCase() === targetCat || opt.text.toLowerCase() === targetCat) {
                        catSelect.selectedIndex = i;
                        found = true;
                        break;
                    }
                }

                // Nếu vẫn không thấy, thử dùng giá trị gốc
                if (!found) catSelect.value = p.category_slug || p.category;

                document.getElementById('p_price').value = p.price;
                document.getElementById('p_original_price').value = p.original_price || '';
                document.getElementById('p_status').value = p.status || 'active';
                document.getElementById('p_image').value = p.image || '';
                setProductImagePreview(p.image || '');
                document.getElementById('p_desc').value = p.feature_text || '';
                const cardFeatures = Array.isArray(p.card_features) ? p.card_features : [];
                document.querySelectorAll('.card-feature-input').forEach((input, idx) => {
                    input.value = cardFeatures[idx] || '';
                });
                document.getElementById('p_detail_desc').value = p.description || '';
                document.getElementById('p_detail_desc_editor').innerHTML = p.description || '';
                document.getElementById('p_seo_slug').value = p.seo_slug || '';
                document.getElementById('p_seo_title').value = p.seo_title || '';
                document.getElementById('p_seo_description').value = p.seo_description || '';
                document.getElementById('p_seo_keywords').value = p.seo_keywords || '';

                renderVariants(p.options || []);

                document.getElementById('modalTitle').innerText = "Chỉnh sửa Sản phẩm";
                getProductModal().show();
            }
        }

        function addVariantRow(data = { name: '', price: '', original_price: '', stock: '', is_upgrade: 0, require_password: 1 }) {
            const container = document.getElementById('variant-container');

            // Clear placeholder if exists
            if (container.querySelector('p.text-muted')) {
                container.innerHTML = '';
            }

            const productId = document.getElementById('p_id').value;
            const variantIdx = container.querySelectorAll('.variant-row').length;

            const row = document.createElement('div');
            row.className = 'variant-row row g-2 mb-2 pb-2 border-bottom align-items-center';
            row.dataset.variantIdx = variantIdx;

            const requirePass = data.require_password === undefined ? 1 : (data.require_password == 1 ? 1 : 0);

            row.innerHTML = `
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm v-name" placeholder="Tên loại" value="${data.name}" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-sm v-price" placeholder="Giá bán" value="${data.price}" min="0" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-sm v-original-price" placeholder="Giá gốc" value="${data.original_price || ''}" min="0" title="Để trống nếu không giảm giá">
                </div>
                <div class="col-md-1">
                    <input type="number" class="form-control form-control-sm v-stock" placeholder="Kho" value="${data.stock}" min="0" required>
                </div>
                <div class="col-md-3 d-flex justify-content-center align-items-center gap-2">
                    <div class="form-check form-switch mb-0" title="Yêu cầu nâng cấp chính chủ">
                        <input class="form-check-input v-upgrade" type="checkbox" ${data.is_upgrade == 1 ? 'checked' : ''}>
                        <label class="form-check-label smaller">Up</label>
                    </div>
                    <div class="form-check form-switch mb-0 ${data.is_upgrade == 1 ? '' : 'd-none'} v-pass-container" title="Yêu cầu mật khẩu khi nâng cấp">
                        <input class="form-check-input v-require-password" type="checkbox" ${requirePass == 1 ? 'checked' : ''}>
                        <label class="form-check-label smaller">MK</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-dark v-stock-btn" title="Quản lý kho" ${productId ? '' : 'disabled'}>
                        <i class="fa-solid fa-warehouse"></i>
                    </button>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('.variant-row').remove(); checkEmptyVariants();">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);

            // Toggle MK switch when Up is checked/unchecked
            row.querySelector('.v-upgrade').addEventListener('change', (e) => {
                const passContainer = row.querySelector('.v-pass-container');
                if (e.target.checked) {
                    passContainer.classList.remove('d-none');
                } else {
                    passContainer.classList.add('d-none');
                }
            });

            // Bind stock button
            row.querySelector('.v-stock-btn').addEventListener('click', () => {
                const upgradeOn = row.querySelector('.v-upgrade').checked;
                if (upgradeOn) {
                    AppNotify.info('Gói nâng cấp chính chủ không dùng kho cấp account.', 'Không cần kho');
                    return;
                }
                const idx = Array.from(container.querySelectorAll('.variant-row')).indexOf(row);
                openStockModal(productId, idx, row.querySelector('.v-name').value || ('Gói ' + (idx + 1)));
            });
        }

        function renderVariants(variants) {
            const container = document.getElementById('variant-container');
            container.innerHTML = '';
            if (variants && variants.length > 0) {
                variants.forEach(v => addVariantRow(v));
            } else {
                checkEmptyVariants();
            }
        }

        function checkEmptyVariants() {
            const container = document.getElementById('variant-container');
            if (container.children.length === 0) {
                container.innerHTML = '<p class="text-muted small mb-0 text-center">Chưa có loại nào. Nhấn "Thêm loại" để bắt đầu.</p>';
            }
        }

        function productDetailTemplate(type) {
            if (type === 'table') {
                return '<table><thead><tr><th>Gói dịch vụ</th><th>Thời hạn</th><th>Hình thức</th><th>Phù hợp với</th></tr></thead><tbody><tr><td>Gói 1</td><td>1 tháng</td><td>Tự động</td><td>Cá nhân</td></tr></tbody></table><p><br></p>';
            }
            if (type === 'cta') {
                return '<h2>Mua hàng tự động 24/7 tại AI CỦA TÔI</h2><p>Chọn gói phù hợp, thanh toán QR và nhận sản phẩm tự động sau khi giao dịch thành công. Cần hỗ trợ nhanh, liên hệ Zalo 0569012134 hoặc Telegram @specademy.</p>';
            }
            return '<h1>Tên sản phẩm chuẩn SEO</h1><p>Sapo ngắn giới thiệu lợi ích chính và từ khóa sản phẩm.</p><h2>Vì sao nên mua tại AI CỦA TÔI?</h2><ul><li>Giao hàng tự động 24/7 sau thanh toán.</li><li>Bảo hành 1 đổi 1 trong thời gian sử dụng.</li><li>Hỗ trợ nhanh qua Zalo 0569012134 hoặc Telegram @specademy.</li></ul><h2>Tính năng và lợi ích nổi bật</h2><ul><li></li><li></li><li></li></ul><h2>Bảng giá và tùy chọn gói</h2>' + productDetailTemplate('table') + '<h2>Chính sách bảo hành</h2><ul><li>Bảo hành 1 đổi 1 nếu lỗi kỹ thuật.</li><li>Hỗ trợ trong suốt thời gian sử dụng.</li></ul><h2>Hướng dẫn mua hàng</h2><ol><li>Chọn gói trên aicuatoi.net.</li><li>Thanh toán bằng QR ngân hàng.</li><li>Hệ thống xác nhận và giao hàng tự động.</li></ol>';
        }

        document.querySelectorAll('.product-detail-toolbar button').forEach(btn => {
            btn.addEventListener('mousedown', e => e.preventDefault());
            btn.addEventListener('click', () => {
                const editor = document.getElementById('p_detail_desc_editor');
                if (!editor) return;
                editor.focus();
                if (btn.dataset.template) {
                    document.execCommand('insertHTML', false, productDetailTemplate(btn.dataset.template));
                } else {
                    document.execCommand(btn.dataset.cmd, false, btn.dataset.arg || null);
                }
            });
        });

        document.getElementById('p_detail_desc_editor')?.addEventListener('paste', handleRichEditorPaste);

        function saveProduct() {
            const variants = [];
            document.querySelectorAll('.variant-row').forEach(row => {
                const original = parseFloat(row.querySelector('.v-original-price').value) || 0;
                const price    = parseFloat(row.querySelector('.v-price').value) || 0;
                const requirePasswordCheckbox = row.querySelector('.v-require-password');
                variants.push({
                    name: row.querySelector('.v-name').value,
                    price: price,
                    original_price: original > price ? original : 0,
                    stock: row.querySelector('.v-stock').value,
                    is_upgrade: row.querySelector('.v-upgrade').checked ? 1 : 0,
                    require_password: requirePasswordCheckbox ? (requirePasswordCheckbox.checked ? 1 : 0) : 1
                });
            });

            const categorySelect = document.getElementById('p_category');
            const categoryName = categorySelect.options[categorySelect.selectedIndex].text;

            const formData = new FormData();
            formData.append('id', document.getElementById('p_id').value);
            formData.append('title', document.getElementById('p_title').value);
            formData.append('category', categorySelect.value);
            formData.append('category_name', categoryName);
            formData.append('price', document.getElementById('p_price').value);
            formData.append('original_price', document.getElementById('p_original_price').value);
            formData.append('status', document.getElementById('p_status').value);
            formData.append('image', document.getElementById('p_image').value);
            const fileInput = document.getElementById('p_image_file');
            if (fileInput && fileInput.files[0]) {
                formData.append('image_file', fileInput.files[0]);
            }
            formData.append('desc', document.getElementById('p_desc').value);
            document.querySelectorAll('.card-feature-input').forEach((input, idx) => {
                formData.append('card_feature_' + (idx + 1), input.value);
            });
            document.getElementById('p_detail_desc').value = document.getElementById('p_detail_desc_editor').innerHTML.trim();
            formData.append('description', document.getElementById('p_detail_desc').value);
            formData.append('variants', JSON.stringify(variants));
            formData.append('seo_slug', document.getElementById('p_seo_slug').value);
            formData.append('seo_title', document.getElementById('p_seo_title').value);
            formData.append('seo_description', document.getElementById('p_seo_description').value);
            formData.append('seo_keywords', document.getElementById('p_seo_keywords').value);

            fetch('?action=adminSaveProduct', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: (() => { formData.append('csrf_token', APP_STATE.csrfToken); return formData; })(),
                credentials: 'same-origin'
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        toastMsg('Đã lưu sản phẩm thành công!');
                        getProductModal().hide();
                        location.reload();
                    } else {
                        AppNotify.error(data.message || 'Không thể lưu sản phẩm.', 'Lỗi lưu');
                    }
                });
        }

        function deleteProduct(id) {
            Swal.fire({
                title: 'Xóa sản phẩm này?',
                text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Đồng ý xóa'
            }).then((result) => {
                if (result.isConfirmed) {
                    apiPost('adminDeleteProduct', { id })
                        .then(data => {
                            if (data.success) {
                                toastMsg('Đã xóa sản phẩm!');
                                location.reload();
                            } else {
                                AppNotify.error(data.message || 'Không thể xóa.', 'Lỗi xóa');
                            }
                        });
                }
            })
        }

        function loadSePayDebug() {
            const out = document.getElementById('sepay-debug-output');
            if (!out) return;
            out.classList.remove('d-none');
            out.textContent = 'Đang tải webhook gần nhất...';
            fetch('?action=sepayDebug', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    out.textContent = JSON.stringify(data, null, 2);
                    if (data.result === 'completed') {
                        AppNotify.success('Webhook cuối đã duyệt đơn thành công.', 'SePay OK');
                    } else if (data.result === 'unauthorized') {
                        AppNotify.error('Webhook bị từ chối xác thực. Kiểm tra API Key/Auth method trên SePay.', 'SePay lỗi');
                    } else if (data.result === 'underpaid') {
                        AppNotify.error('Giao dịch thiếu tiền so với đơn hàng.', 'SePay lỗi');
                    }
                })
                .catch(() => {
                    out.textContent = 'Không đọc được debug webhook.';
                    AppNotify.error('Không đọc được debug webhook.', 'SePay lỗi');
                });
        }

        function saveSettings() {
            const formData = new FormData();
            formData.append('heroDesc', document.getElementById('st_heroDesc').value);
            formData.append('bannerText', document.getElementById('st_bannerText').value);
            formData.append('zalo', document.getElementById('st_zalo').value);
            formData.append('footerDesc', document.getElementById('st_footerDesc').value);
            formData.append('socialLink', document.getElementById('st_socialLink').value);
            formData.append('copyright', document.getElementById('st_copyright').value);
            formData.append('terms_of_service', document.getElementById('st_terms_of_service').value);
            formData.append('privacy_policy', document.getElementById('st_privacy_policy').value);

            // SePay Settings
            formData.append('sepay_active', document.getElementById('st_sepay_active').checked ? '1' : '0');
            formData.append('demo_payment_active', document.getElementById('st_demo_payment_active').checked ? '1' : '0');
            formData.append('sepay_mode', document.getElementById('st_sepay_mode').value);
            formData.append('sepay_token', document.getElementById('st_sepay_token').value);
            formData.append('sepay_merchant_id', document.getElementById('st_sepay_merchant_id').value);
            formData.append('sepay_api_key', document.getElementById('st_sepay_api_key').value);
            formData.append('bank_id', document.getElementById('st_bank_id').value);
            formData.append('bank_account', document.getElementById('st_bank_account').value);
            formData.append('bank_name', document.getElementById('st_bank_name').value);

            // About & Contact Settings
            formData.append('about_title', document.getElementById('st_about_title').value);
            formData.append('about_desc', document.getElementById('st_about_desc').value);
            formData.append('about_image', document.getElementById('st_about_image').value);
            formData.append('about_stat_value', document.getElementById('st_about_stat_value').value);
            formData.append('about_stat_label', document.getElementById('st_about_stat_label').value);

            const aboutFeatures = [];
            document.querySelectorAll('.about-feature-row').forEach(row => {
                aboutFeatures.push({
                    icon: row.querySelector('.af-icon').value,
                    color: row.querySelector('.af-color').value,
                    title: row.querySelector('.af-title').value,
                    desc: row.querySelector('.af-desc').value
                });
            });
            formData.append('about_features', JSON.stringify(aboutFeatures));

            formData.append('contact_title', document.getElementById('st_contact_title').value);
            formData.append('contact_desc', document.getElementById('st_contact_desc').value);

            const contactMethods = [];
            document.querySelectorAll('.contact-method-row').forEach(row => {
                contactMethods.push({
                    icon: row.querySelector('.cm-icon').value,
                    text: row.querySelector('.cm-text').value
                });
            });
            formData.append('contact_methods', JSON.stringify(contactMethods));

            const socialLinks = [];
            document.querySelectorAll('.social-link-row').forEach(row => {
                socialLinks.push({
                    icon: row.querySelector('.sl-icon').value,
                    url: row.querySelector('.sl-url').value
                });
            });
            formData.append('social_links_json', JSON.stringify(socialLinks));

            // Telegram Bot settings
            const botToken = document.getElementById('st_telegram_bot_token').value;
            const chatId = document.getElementById('st_telegram_chat_id').value;
            formData.append('telegram_bot_token', botToken);
            formData.append('telegram_chat_id', chatId);

            // SMTP Settings
            const smtpHost = document.getElementById('st_smtp_host').value;
            const smtpPort = document.getElementById('st_smtp_port').value;
            const smtpSecure = document.getElementById('st_smtp_secure').value;
            const smtpFromName = document.getElementById('st_smtp_from_name').value;
            const smtpUser = document.getElementById('st_smtp_user').value;
            const smtpPass = document.getElementById('st_smtp_pass').value;
            const smtpFromEmail = document.getElementById('st_smtp_from_email').value;
            const smtpDefaultSubject = document.getElementById('st_smtp_default_subject').value;
            const smtpDefaultBody = document.getElementById('st_smtp_default_body').value;

            formData.append('smtp_host', smtpHost);
            formData.append('smtp_port', smtpPort);
            formData.append('smtp_secure', smtpSecure);
            formData.append('smtp_from_name', smtpFromName);
            formData.append('smtp_user', smtpUser);
            formData.append('smtp_pass', smtpPass);
            formData.append('smtp_from_email', smtpFromEmail);
            formData.append('smtp_default_subject', smtpDefaultSubject);
            formData.append('smtp_default_body', smtpDefaultBody);

            fetch('?action=adminSaveSettings', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: (() => { formData.append('csrf_token', APP_STATE.csrfToken); return formData; })(),
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        APP_STATE.settings['telegram_bot_token'] = botToken;
                        APP_STATE.settings['telegram_chat_id'] = chatId;
                        APP_STATE.settings['smtp_host'] = smtpHost;
                        APP_STATE.settings['smtp_port'] = smtpPort;
                        APP_STATE.settings['smtp_secure'] = smtpSecure;
                        APP_STATE.settings['smtp_from_name'] = smtpFromName;
                        APP_STATE.settings['smtp_user'] = smtpUser;
                        APP_STATE.settings['smtp_pass'] = smtpPass;
                        APP_STATE.settings['smtp_from_email'] = smtpFromEmail;
                        APP_STATE.settings['smtp_default_subject'] = smtpDefaultSubject;
                        APP_STATE.settings['smtp_default_body'] = smtpDefaultBody;
                        AppNotify.success('Cấu hình website đã được cập nhật.', 'Lưu thành công');
                    } else {
                        AppNotify.error(data.message || 'Không thể lưu cấu hình.', 'Lỗi lưu');
                    }
                });
        }

        function saveTelegramSettings() {
            const fd = new FormData();
            const botToken = document.getElementById('st_telegram_bot_token').value;
            const chatId = document.getElementById('st_telegram_chat_id').value;
            fd.append('telegram_bot_token', botToken);
            fd.append('telegram_chat_id', chatId);
            
            // Must include all allowed keys - send current values for everything else
            ['bannerText','zalo','footerDesc','heroDesc','socialLink','copyright','terms_of_service','privacy_policy',
             'sepay_active','sepay_mode','sepay_token','sepay_merchant_id','sepay_api_key',
             'bank_id','bank_account','bank_name','about_title','about_desc','about_image',
             'about_stat_value','about_stat_label','about_features','contact_title','contact_desc',
             'contact_methods','social_links_json','demo_payment_active',
             'smtp_host','smtp_port','smtp_secure','smtp_from_name','smtp_user','smtp_pass',
             'smtp_from_email','smtp_default_subject','smtp_default_body'].forEach(k => {
                const el = document.getElementById('st_' + k);
                if (el) fd.append(k, el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value);
                else fd.append(k, APP_STATE.settings[k] || '');
            });
            fd.append('csrf_token', APP_STATE.csrfToken);
            fetch('?action=adminSaveSettings', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: fd, credentials: 'same-origin'
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    APP_STATE.settings['telegram_bot_token'] = botToken;
                    APP_STATE.settings['telegram_chat_id'] = chatId;
                    AppNotify.success('Cấu hình Telegram đã được lưu.', 'Lưu thành công');
                } else {
                    AppNotify.error(d.message || 'Không thể lưu.', 'Lỗi');
                }
            });
        }

        function saveSmtpSettings() {
            const fd = new FormData();
            const smtpHost = document.getElementById('st_smtp_host').value;
            const smtpPort = document.getElementById('st_smtp_port').value;
            const smtpSecure = document.getElementById('st_smtp_secure').value;
            const smtpFromName = document.getElementById('st_smtp_from_name').value;
            const smtpUser = document.getElementById('st_smtp_user').value;
            const smtpPass = document.getElementById('st_smtp_pass').value;
            const smtpFromEmail = document.getElementById('st_smtp_from_email').value;
            const smtpDefaultSubject = document.getElementById('st_smtp_default_subject').value;
            const smtpDefaultBody = document.getElementById('st_smtp_default_body').value;

            fd.append('smtp_host', smtpHost);
            fd.append('smtp_port', smtpPort);
            fd.append('smtp_secure', smtpSecure);
            fd.append('smtp_from_name', smtpFromName);
            fd.append('smtp_user', smtpUser);
            fd.append('smtp_pass', smtpPass);
            fd.append('smtp_from_email', smtpFromEmail);
            fd.append('smtp_default_subject', smtpDefaultSubject);
            fd.append('smtp_default_body', smtpDefaultBody);
            
            // Send existing values for other fields
            ['bannerText','zalo','footerDesc','heroDesc','socialLink','copyright','terms_of_service','privacy_policy',
             'sepay_active','sepay_mode','sepay_token','sepay_merchant_id','sepay_api_key',
             'bank_id','bank_account','bank_name','about_title','about_desc','about_image',
             'about_stat_value','about_stat_label','about_features','contact_title','contact_desc',
             'contact_methods','social_links_json','demo_payment_active',
             'telegram_bot_token','telegram_chat_id'].forEach(k => {
                const el = document.getElementById('st_' + k);
                if (el) fd.append(k, el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value);
                else fd.append(k, APP_STATE.settings[k] || '');
            });
            fd.append('csrf_token', APP_STATE.csrfToken);
            
            fetch('?action=adminSaveSettings', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: fd, credentials: 'same-origin'
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    APP_STATE.settings['smtp_host'] = smtpHost;
                    APP_STATE.settings['smtp_port'] = smtpPort;
                    APP_STATE.settings['smtp_secure'] = smtpSecure;
                    APP_STATE.settings['smtp_from_name'] = smtpFromName;
                    APP_STATE.settings['smtp_user'] = smtpUser;
                    APP_STATE.settings['smtp_pass'] = smtpPass;
                    APP_STATE.settings['smtp_from_email'] = smtpFromEmail;
                    APP_STATE.settings['smtp_default_subject'] = smtpDefaultSubject;
                    APP_STATE.settings['smtp_default_body'] = smtpDefaultBody;
                    AppNotify.success('Cấu hình Email SMTP đã được lưu.', 'Lưu thành công');
                } else {
                    AppNotify.error(d.message || 'Không thể lưu.', 'Lỗi');
                }
            });
        }

        function testSmtp() {
            Swal.fire({
                title: 'Gửi Email thử nghiệm',
                text: 'Nhập địa chỉ email nhận thư thử nghiệm:',
                input: 'email',
                inputPlaceholder: 'email_cua_ban@example.com',
                showCancelButton: true,
                confirmButtonText: 'Gửi thử',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#111',
                preConfirm: (email) => {
                    if (!email) {
                        Swal.showValidationMessage('Vui lòng nhập địa chỉ email hợp lệ');
                    }
                    return email;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const email = result.value;
                    const btn = document.getElementById('btnSmtpTest');
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang gửi...';

                    const fd = new FormData();
                    fd.append('test_email', email);
                    fd.append('csrf_token', APP_STATE.csrfToken);

                    fetch('?action=adminSmtpTest', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                        body: fd,
                        credentials: 'same-origin'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            AppNotify.success(data.message || 'Đã gửi email thử nghiệm thành công! Vui lòng kiểm tra hộp thư.');
                        } else {
                            AppNotify.error(data.message || 'Lỗi gửi email thử nghiệm.');
                        }
                    })
                    .catch(() => AppNotify.error('Không thể kết nối server.'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Gửi thử Email';
                    });
                }
            });
        }

        function testTelegram() {
            const btn = document.getElementById('btnTelegramTest');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang gửi...';
            const fd = new FormData();
            fd.append('csrf_token', APP_STATE.csrfToken);
            fetch('?action=adminTelegramTest', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: fd, credentials: 'same-origin'
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    AppNotify.success(d.message || 'Gửi test thành công!', 'Telegram OK ✅');
                } else {
                    AppNotify.error(d.message || 'Gửi thất bại.', 'Telegram lỗi ❌');
                }
            }).catch(() => AppNotify.error('Không thể kết nối server.', 'Lỗi mạng'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-brands fa-telegram me-2"></i>Test kết nối';
            });
        }

        function addAboutFeatureRow(data = { icon: 'fa-bolt', color: 'text-warning', title: '', desc: '' }) {
            const container = document.getElementById('about-features-container');
            const row = document.createElement('div');
            row.className = 'about-feature-row row g-2 mb-2 pb-2 border-bottom align-items-center';
            row.innerHTML = `
                <div class="col-md-2">
                    <input type="text" class="form-control form-control-sm af-icon" placeholder="Icon" value="${data.icon}" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control form-control-sm af-color" placeholder="Color" value="${data.color}" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm af-title" placeholder="Tiêu đề" value="${data.title}" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm af-desc" placeholder="Mô tả" value="${data.desc}" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('.about-feature-row').remove();">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
        }

        function addContactMethodRow(data = { icon: 'fa-phone', text: '' }) {
            const container = document.getElementById('contact-methods-container');
            const row = document.createElement('div');
            row.className = 'contact-method-row row g-2 mb-2 pb-2 border-bottom align-items-center';
            row.innerHTML = `
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm cm-icon" placeholder="Icon" value="${data.icon}" required>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control form-control-sm cm-text" placeholder="Nội dung" value="${data.text}" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('.contact-method-row').remove();">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
        }

        function addSocialLinkRow(data = { icon: 'fa-facebook-f', url: '' }) {
            const container = document.getElementById('social-links-container');
            const row = document.createElement('div');
            row.className = 'social-link-row row g-2 mb-2 pb-2 border-bottom align-items-center';
            row.innerHTML = `
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm sl-icon" placeholder="Icon" value="${data.icon}" required>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control form-control-sm sl-url" placeholder="URL" value="${data.url}" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('.social-link-row').remove();">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
        }

        // Initialize About & Contact rows
        document.addEventListener("DOMContentLoaded", () => {
            const aboutFeatures = JSON.parse(APP_STATE.settings.about_features || '[]');
            if (aboutFeatures.length > 0) aboutFeatures.forEach(f => addAboutFeatureRow(f));
            else { addAboutFeatureRow(); addAboutFeatureRow(); }

            const contactMethods = JSON.parse(APP_STATE.settings.contact_methods || '[]');
            if (contactMethods.length > 0) contactMethods.forEach(m => addContactMethodRow(m));
            else { addContactMethodRow(); addContactMethodRow(); }

            const socialLinks = JSON.parse(APP_STATE.settings.social_links_json || '[]');
            if (socialLinks.length > 0) socialLinks.forEach(s => addSocialLinkRow(s));
            else { addSocialLinkRow(); addSocialLinkRow(); }
        });

        function toastMsg(msg, type) {
            if (type === 'error') {
                AppNotify.error(msg);
            } else {
                AppNotify.success(msg);
            }
        }

        function openCategoryModal() {
            document.getElementById('categoryForm').reset();
            document.getElementById('cat_id').value = '';
            document.getElementById('cat_seo_slug').value = '';
            document.getElementById('cat_seo_title').value = '';
            document.getElementById('cat_seo_description').value = '';
            document.getElementById('cat_seo_keywords').value = '';
            document.getElementById('catModalTitle').innerText = "Thêm Danh mục mới";
            getCategoryModal().show();
        }

        function editCategory(id) {
            const cat = APP_STATE.categories.find(c => c.id == id);
            if (cat) {
                document.getElementById('cat_id').value = cat.id;
                document.getElementById('cat_name').value = cat.name;
                document.getElementById('cat_slug').value = cat.slug;
                document.getElementById('cat_is_pro').value = cat.is_pro ? '1' : '0';
                document.getElementById('cat_icon').value = cat.icon || '';
                document.getElementById('cat_icon_color').value = cat.icon_color || '';
                document.getElementById('cat_seo_slug').value = cat.seo_slug || '';
                document.getElementById('cat_seo_title').value = cat.seo_title || '';
                document.getElementById('cat_seo_description').value = cat.seo_description || '';
                document.getElementById('cat_seo_keywords').value = cat.seo_keywords || '';

                document.getElementById('catModalTitle').innerText = "Chỉnh sửa Danh mục";
                getCategoryModal().show();
            }
        }

        function saveCategory() {
            const formData = new FormData();
            formData.append('id', document.getElementById('cat_id').value);
            formData.append('name', document.getElementById('cat_name').value);
            formData.append('slug', document.getElementById('cat_slug').value);
            formData.append('is_pro', document.getElementById('cat_is_pro').value);
            formData.append('icon', document.getElementById('cat_icon').value);
            formData.append('icon_color', document.getElementById('cat_icon_color').value);
            formData.append('seo_slug', document.getElementById('cat_seo_slug').value);
            formData.append('seo_title', document.getElementById('cat_seo_title').value);
            formData.append('seo_description', document.getElementById('cat_seo_description').value);
            formData.append('seo_keywords', document.getElementById('cat_seo_keywords').value);

            fetch('?action=adminSaveCategory', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: (() => { formData.append('csrf_token', APP_STATE.csrfToken); return formData; })(),
                credentials: 'same-origin'
            })
                .then(async res => {
                    const text = await res.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Server không trả JSON. Kiểm tra APP_DEBUG=false hoặc lỗi PHP phía server.');
                    }
                })
                .then(data => {
                    if (data.success) {
                        toastMsg('Đã lưu danh mục thành công!');
                        getCategoryModal().hide();
                        location.reload();
                    } else {
                        AppNotify.error(data.message || 'Không thể lưu danh mục.', 'Lỗi lưu');
                    }
                })
                .catch(err => AppNotify.error(err.message || 'Không thể lưu danh mục.', 'Lỗi mạng'));
        }

        function deleteCategory(id) {
            Swal.fire({
                title: 'Xóa danh mục này?',
                text: "Các sản phẩm thuộc danh mục này sẽ không bị xóa.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Xóa ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    apiPost('adminDeleteCategory', { id })
                        .then(data => {
                            if (data.success) {
                                toastMsg('Đã xóa danh mục!');
                                location.reload();
                            } else {
                                AppNotify.error(data.message || 'Không thể xóa.', 'Lỗi xóa');
                            }
                        });
                }
            });
        }
        function handleBlogSearch() {
            blogFilterKeyword = (document.getElementById('blog-search-input')?.value || '').toLowerCase().trim();
            blogsCurrentPage = 1;
            renderBlogsTable();
        }

        function changeBlogsPage(page) {
            blogsCurrentPage = page;
            renderBlogsTable();
        }

        function renderBlogsTable() {
            const tbody = document.getElementById('blog-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            let blogs = APP_STATE.blogs || [];
            if (blogFilterKeyword) {
                blogs = blogs.filter(b => 
                    (b.title || '').toLowerCase().includes(blogFilterKeyword) ||
                    (b.description || '').toLowerCase().includes(blogFilterKeyword)
                );
            }

            const total = blogs.length;
            const totalPages = Math.max(1, Math.ceil(total / blogsPerPage));
            if (blogsCurrentPage > totalPages) blogsCurrentPage = totalPages;

            const startIdx = (blogsCurrentPage - 1) * blogsPerPage;
            const endIdx = Math.min(startIdx + blogsPerPage, total);
            const pagedBlogs = blogs.slice(startIdx, endIdx);

            const countStart = document.getElementById('blog-count-start');
            const countEnd = document.getElementById('blog-count-end');
            const countTotal = document.getElementById('blog-count-total');
            if (countStart) countStart.innerText = total === 0 ? 0 : startIdx + 1;
            if (countEnd) countEnd.innerText = endIdx;
            if (countTotal) countTotal.innerText = total;

            renderPaginationControls('blog-pagination-container', blogsCurrentPage, totalPages, 'changeBlogsPage');

            if (pagedBlogs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="table-empty-state"><i class="fa-solid fa-newspaper"></i><div>Không tìm thấy bài viết nào.</div></td></tr>`;
                return;
            }

            pagedBlogs.forEach(blog => {
                const dateStr = blog.created_at ? new Date(blog.created_at.replace(' ', 'T')).toLocaleDateString('vi-VN') : '';
                const escTitle = escapeHtml(blog.title || '');
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${escapeHtml(blog.image || '')}" class="img-thumbnail-custom me-3" style="width: 80px; height: 45px; object-fit: cover;" alt="${escTitle}" onerror="this.onerror=null; this.src=FALLBACK_PRODUCT_IMAGE;">
                                <div class="fw-bold text-dark">${escTitle}</div>
                            </div>
                        </td>
                        <td class="text-muted small">${dateStr}</td>
                        <td class="text-end">
                            <button class="btn-action" onclick="editBlog(${blog.id})" title="Sửa"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="deleteBlog(${blog.id})" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }

        function openBlogModal() {
            document.getElementById('blogForm').reset();
            document.getElementById('blog_id').value = '';
            document.getElementById('blog_image_url').value = '';
            document.getElementById('blog_desc').value = '';
            document.getElementById('blog_content').value = '';
            document.getElementById('blog_content_editor').innerHTML = '';
            document.getElementById('blog_seo_slug').value = '';
            document.getElementById('blog_seo_title').value = '';
            document.getElementById('blog_seo_description').value = '';
            document.getElementById('blog_seo_keywords').value = '';
            setBlogImagePreview('');
            updateBlogSeoChecklist();
            document.querySelector('#blogModal .modal-title').innerText = 'Thêm bài viết mới';
            getBlogModal().show();
        }

        function editBlog(id) {
            const blog = APP_STATE.blogs.find(b => b.id == id);
            if (!blog) return;
            document.getElementById('blog_id').value = blog.id;
            document.getElementById('blog_title').value = blog.title || '';
            document.getElementById('blog_image_url').value = blog.image || '';
            document.getElementById('blog_desc').value = blog.description || '';
            document.getElementById('blog_content_editor').innerHTML = blog.content || blog.description || '';
            document.getElementById('blog_content').value = blog.content || blog.description || '';
            document.getElementById('blog_seo_slug').value = blog.seo_slug || '';
            document.getElementById('blog_seo_title').value = blog.seo_title || '';
            document.getElementById('blog_seo_description').value = blog.seo_description || '';
            document.getElementById('blog_seo_keywords').value = blog.seo_keywords || '';
            setBlogImagePreview(blog.image || '');
            updateBlogSeoChecklist();
            document.querySelector('#blogModal .modal-title').innerText = 'Chỉnh sửa bài viết';
            getBlogModal().show();
        }

        function setBlogImagePreview(url) {
            const img = document.getElementById('blog_image_preview');
            const placeholder = document.getElementById('blog_image_placeholder');
            const clearBtn = document.getElementById('blog_image_clear');
            if (url) {
                img.src = url;
                img.style.display = 'block';
                placeholder.style.display = 'none';
                clearBtn.classList.remove('d-none');
            } else {
                img.removeAttribute('src');
                img.style.display = 'none';
                placeholder.style.display = 'flex';
                clearBtn.classList.add('d-none');
            }
        }

        // Bind image picker + clear button + rich toolbar once
        function setupBlogModal() {
            const fileInput = document.getElementById('blog_image_file');
            const clearBtn  = document.getElementById('blog_image_clear');
            if (!fileInput || !clearBtn) return;

            fileInput.addEventListener('change', () => {
                const f = fileInput.files[0];
                if (!f) return;
                if (f.size > 10 * 1024 * 1024) {
                    AppNotify.warning('Tối đa 10MB mỗi tệp.', 'Tệp quá lớn');
                    fileInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => setBlogImagePreview(e.target.result);
                reader.readAsDataURL(f);
            });

            clearBtn.addEventListener('click', () => {
                fileInput.value = '';
                document.getElementById('blog_image_url').value = '';
                setBlogImagePreview('');
            });

            // Rich text toolbar -> contenteditable
            const editor = document.getElementById('blog_content_editor');
            if (editor) {
                document.querySelectorAll('#blogModal .rich-toolbar [data-cmd]').forEach(btn => {
                    btn.addEventListener('mousedown', e => e.preventDefault()); // keep selection
                    btn.addEventListener('click', () => {
                        const cmd = btn.dataset.cmd;
                        let arg = btn.dataset.arg || null;
                        if (cmd === 'createLink') {
                            const u = prompt('Nhập URL:');
                            if (!u) return;
                            arg = u;
                        }
                        editor.focus();
                        document.execCommand(cmd, false, arg);
                        updateBlogSeoChecklist();
                    });
                });
            }

            ['blog_title', 'blog_desc', 'blog_content_editor', 'blog_seo_slug', 'blog_seo_title', 'blog_seo_description', 'blog_seo_keywords'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                el.addEventListener('input', updateBlogSeoChecklist);
                el.addEventListener('keyup', updateBlogSeoChecklist);
            });

            editor?.addEventListener('paste', handleRichEditorPaste);
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function markdownTableToHtml(text) {
            const lines = text.replace(/\r\n/g, '\n').split('\n').map(line => line.trim()).filter(Boolean);
            const tableStart = lines.findIndex((line, index) => {
                return line.includes('|') && lines[index + 1] && /^\|?\s*:?-{3,}:?\s*(\|\s*:?-{3,}:?\s*)+\|?$/.test(lines[index + 1]);
            });
            if (tableStart === -1) return '';

            const tableLines = [];
            for (let i = tableStart; i < lines.length; i++) {
                if (!lines[i].includes('|')) break;
                tableLines.push(lines[i]);
            }
            if (tableLines.length < 3) return '';

            const parseRow = line => line.replace(/^\|/, '').replace(/\|$/, '').split('|').map(cell => cell.trim());
            const headers = parseRow(tableLines[0]);
            const rows = tableLines.slice(2).map(parseRow).filter(row => row.length === headers.length);
            if (!headers.length || !rows.length) return '';

            return '<table><thead><tr>'
                + headers.map(cell => `<th>${escapeHtml(cell)}</th>`).join('')
                + '</tr></thead><tbody>'
                + rows.map(row => '<tr>' + row.map(cell => `<td>${escapeHtml(cell)}</td>`).join('') + '</tr>').join('')
                + '</tbody></table><p><br></p>';
        }

        function cleanPastedHtml(html) {
            const template = document.createElement('template');
            template.innerHTML = html;
            const allowed = new Set(['P', 'BR', 'STRONG', 'B', 'EM', 'I', 'U', 'S', 'H1', 'H2', 'H3', 'H4', 'UL', 'OL', 'LI', 'BLOCKQUOTE', 'A', 'TABLE', 'THEAD', 'TBODY', 'TR', 'TH', 'TD']);
            template.content.querySelectorAll('*').forEach(node => {
                if (!allowed.has(node.tagName)) {
                    node.replaceWith(...Array.from(node.childNodes));
                    return;
                }
                Array.from(node.attributes).forEach(attr => {
                    const keepHref = node.tagName === 'A' && attr.name === 'href' && !/^\s*(javascript|data):/i.test(attr.value);
                    if (!keepHref) node.removeAttribute(attr.name);
                });
            });
            return template.innerHTML;
        }

        function handleRichEditorPaste(event) {
            const clipboard = event.clipboardData || window.clipboardData;
            if (!clipboard) return;

            const html = clipboard.getData('text/html');
            const text = clipboard.getData('text/plain');
            const markdownTable = markdownTableToHtml(text);
            const content = html ? cleanPastedHtml(html) : markdownTable;
            if (!content) return;

            event.preventDefault();

            const editor = event.currentTarget;
            const selection = window.getSelection();
            const isAllSelected = selection.toString().trim() === editor.innerText.trim();
            const isEmpty = editor.innerText.trim() === '';

            if (isEmpty || isAllSelected) {
                editor.innerHTML = content;
                const range = document.createRange();
                range.selectNodeContents(editor);
                range.collapse(false);
                selection.removeAllRanges();
                selection.addRange(range);
            } else {
                document.execCommand('insertHTML', false, content);
            }

            if (typeof updateBlogSeoChecklist === 'function') {
                updateBlogSeoChecklist();
            }
        }

        function getBlogSeoIssues() {
            const title = document.getElementById('blog_title').value.trim();
            const summary = document.getElementById('blog_desc').value.trim();
            const contentText = document.getElementById('blog_content_editor').innerText.trim();
            const seoSlug = document.getElementById('blog_seo_slug').value.trim();
            const seoTitle = document.getElementById('blog_seo_title').value.trim();
            const seoDescription = document.getElementById('blog_seo_description').value.trim();
            const keywords = document.getElementById('blog_seo_keywords').value.split(',').map(k => k.trim()).filter(Boolean);
            return [
                { ok: title.length >= 20 && title.length <= 70, text: 'Tiêu đề bài viết nên từ 20-70 ký tự.' },
                { ok: summary.length >= 80 && summary.length <= 180, text: 'Mô tả ngắn nên từ 80-180 ký tự.' },
                { ok: contentText.length >= 600, text: 'Nội dung chi tiết nên tối thiểu 600 ký tự.' },
                { ok: seoSlug === '' || /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(seoSlug), text: 'SEO slug dùng chữ thường, số và dấu gạch ngang.' },
                { ok: seoTitle.length >= 45 && seoTitle.length <= 65, text: 'SEO title nên từ 45-65 ký tự.' },
                { ok: seoDescription.length >= 120 && seoDescription.length <= 165, text: 'SEO description nên từ 120-165 ký tự.' },
                { ok: keywords.length >= 2, text: 'SEO keywords nên có ít nhất 2 từ khóa, cách nhau bằng dấu phẩy.' },
            ];
        }

        function updateBlogSeoChecklist() {
            const list = document.getElementById('blog_seo_checklist');
            const badge = document.getElementById('blog_seo_score');
            if (!list || !badge) return;
            const issues = getBlogSeoIssues();
            const okCount = issues.filter(item => item.ok).length;
            badge.textContent = `${okCount}/${issues.length}`;
            badge.className = 'badge ' + (okCount === issues.length ? 'bg-success' : okCount >= 5 ? 'bg-warning text-dark' : 'bg-danger');
            list.innerHTML = issues.map(item => `
                <li class="${item.ok ? 'ok' : 'warn'}">
                    <i class="fa-solid ${item.ok ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i>
                    <span>${item.text}</span>
                </li>
            `).join('');
        }

        function saveBlog() {
            const editor = document.getElementById('blog_content_editor');
            document.getElementById('blog_content').value = editor.innerHTML.trim();

            const missing = getBlogSeoIssues().filter(item => !item.ok).map(item => item.text);
            if (missing.length) {
                AppNotify.warning('Bài viết chưa chuẩn SEO: ' + missing.join(' '), 'Cần bổ sung');
                return;
            }

            const formData = new FormData();
            formData.append('id', document.getElementById('blog_id').value);
            formData.append('title', document.getElementById('blog_title').value);
            formData.append('image', document.getElementById('blog_image_url').value);
            formData.append('description', document.getElementById('blog_desc').value);
            formData.append('content', document.getElementById('blog_content').value);
            formData.append('seo_slug', document.getElementById('blog_seo_slug').value);
            formData.append('seo_title', document.getElementById('blog_seo_title').value);
            formData.append('seo_description', document.getElementById('blog_seo_description').value);
            formData.append('seo_keywords', document.getElementById('blog_seo_keywords').value);
            const fileInput = document.getElementById('blog_image_file');
            if (fileInput.files[0]) {
                formData.append('image_file', fileInput.files[0]);
            }

            apiPost('adminSaveBlog', formData)
                .then(data => {
                    if (data.success) {
                        toastMsg('Đã lưu bài viết!');
                        getBlogModal().hide();
                        location.reload();
                    } else {
                        AppNotify.error(data.message || 'Không thể lưu bài viết.', 'Lỗi lưu');
                    }
                });
        }

        function deleteBlog(id) {
            Swal.fire({
                title: 'Xóa bài viết này?',
                text: 'Hành động không thể hoàn tác.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Xóa ngay'
            }).then(result => {
                if (!result.isConfirmed) return;
                apiPost('adminDeleteBlog', { id })
                    .then(data => {
                        if (data.success) {
                            toastMsg('Đã xóa bài viết!');
                            location.reload();
                        } else {
                            AppNotify.error(data.message || 'Không thể xóa.', 'Lỗi xóa');
                        }
                    });
            });
        }

        // ============== ADMIN CHAT HELPERS ==============
        function escHtml(s) {
            return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        function filterContactsByStatus(status, btn) {
            contactFilterStatus = status;
            document.querySelectorAll('.contact-tab-btn').forEach(b => {
                b.classList.remove('active', 'btn-dark');
                b.classList.add('btn-outline-dark');
            });
            if (btn) {
                btn.classList.add('active', 'btn-dark');
                btn.classList.remove('btn-outline-dark');
            }
            contactsCurrentPage = 1;
            renderContacts();
        }

        function handleContactSearch() {
            contactFilterKeyword = (document.getElementById('contact-search-input')?.value || '').toLowerCase().trim();
            contactsCurrentPage = 1;
            renderContacts();
        }

        function changeContactsPage(page) {
            contactsCurrentPage = page;
            renderContacts();
        }

        function renderContacts() {
            const tbody = document.getElementById('contact-table-body');
            if (!tbody) return;

            let messages = [...(APP_STATE.contactMessages || [])].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            if (contactFilterStatus && contactFilterStatus !== 'all') {
                messages = messages.filter(m => m.status === contactFilterStatus);
            }
            if (contactFilterKeyword) {
                messages = messages.filter(m => 
                    (m.name || '').toLowerCase().includes(contactFilterKeyword) ||
                    (m.email || '').toLowerCase().includes(contactFilterKeyword) ||
                    (m.subject || '').toLowerCase().includes(contactFilterKeyword) ||
                    (m.message || '').toLowerCase().includes(contactFilterKeyword)
                );
            }

            const total = messages.length;
            const totalPages = Math.max(1, Math.ceil(total / contactsPerPage));
            if (contactsCurrentPage > totalPages) contactsCurrentPage = totalPages;

            const startIdx = (contactsCurrentPage - 1) * contactsPerPage;
            const endIdx = Math.min(startIdx + contactsPerPage, total);
            const pagedMessages = messages.slice(startIdx, endIdx);

            const countStart = document.getElementById('contact-count-start');
            const countEnd = document.getElementById('contact-count-end');
            const countTotal = document.getElementById('contact-count-total');
            if (countStart) countStart.innerText = total === 0 ? 0 : startIdx + 1;
            if (countEnd) countEnd.innerText = endIdx;
            if (countTotal) countTotal.innerText = total;

            renderPaginationControls('contact-pagination-container', contactsCurrentPage, totalPages, 'changeContactsPage');

            if (pagedMessages.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="table-empty-state"><i class="fa-solid fa-inbox"></i><div>Không tìm thấy tin liên hệ nào.</div></td></tr>`;
                updateContactBadge();
                return;
            }

            tbody.innerHTML = pagedMessages.map(msg => {
                const isNew = msg.status === 'new';
                const statusBadge = isNew
                    ? '<span class="badge bg-warning text-dark rounded-pill">Mới</span>'
                    : (msg.status === 'archived'
                        ? '<span class="badge bg-secondary rounded-pill">Lưu trữ</span>'
                        : '<span class="badge bg-success rounded-pill">Đã đọc</span>');
                const dateStr = msg.created_at ? new Date(String(msg.created_at).replace(' ', 'T')).toLocaleString('vi-VN') : '';
                return `
                    <tr class="${isNew ? 'table-warning' : ''}">
                        <td>
                            <div class="fw-semibold text-dark">${escHtml(msg.name || '')}</div>
                            <a class="small text-muted" href="mailto:${escHtml(msg.email || '')}">${escHtml(msg.email || '')}</a>
                        </td>
                        <td class="fw-semibold text-dark">${escHtml(msg.subject || '')}</td>
                        <td style="max-width: 420px;">
                            <div class="text-muted small" style="white-space: pre-wrap;">${escHtml(msg.message || '')}</div>
                        </td>
                        <td>${statusBadge}</td>
                        <td class="small text-muted">${dateStr}</td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-dark" onclick="setContactStatus(${Number(msg.id)}, 'read')" title="Đánh dấu đã đọc"><i class="fa-solid fa-check"></i></button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="setContactStatus(${Number(msg.id)}, 'archived')" title="Lưu trữ"><i class="fa-solid fa-box-archive"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            updateContactBadge();
        }

        function updateContactBadge(count) {
            const badge = document.getElementById('contact-unread-badge');
            if (!badge) return;
            const unread = typeof count === 'number'
                ? count
                : (APP_STATE.contactMessages || []).filter(msg => msg.status === 'new').length;
            badge.textContent = unread;
            badge.classList.toggle('d-none', unread <= 0);
        }

        function setContactStatus(id, status) {
            apiPost('adminUpdateContactStatus', { id, status }).then(data => {
                if (!data.success) {
                    AppNotify.error(data.message || 'Không thể cập nhật tin liên hệ.', 'Lỗi');
                    return;
                }
                const msg = (APP_STATE.contactMessages || []).find(item => Number(item.id) === Number(id));
                if (msg) msg.status = status;
                renderContacts();
                updateContactBadge(Number(data.unreadContacts || 0));
                AppNotify.success('Đã cập nhật tin liên hệ.', 'Thành công');
            }).catch(() => AppNotify.error('Không thể cập nhật tin liên hệ.', 'Lỗi kết nối'));
        }
        // ============== STOCK MANAGER ==============
        var stockCtx = { productId: '', variantIdx: 0 };

        function openStockModal(productId, variantIdx, label) {
            if (!productId) {
                AppNotify.info('Bạn cần lưu sản phẩm rồi mở lại để quản lý kho theo từng gói.', 'Hãy lưu sản phẩm trước');
                return;
            }
            stockCtx = { productId, variantIdx };
            document.getElementById('stock-modal-subtitle').textContent = label || '';
            document.getElementById('stock-input-textarea').value = '';

            // Bootstrap 5 doesn't stack modals natively. Hide the parent product
            // modal first, then show the stock modal. Restore when closed.
            const productEl = document.getElementById('productModal');
            const wasOpen = productEl && productEl.classList.contains('show');
            if (wasOpen) {
                getProductModal().hide();
                document.getElementById('stockModal').addEventListener('hidden.bs.modal', function once() {
                    document.getElementById('stockModal').removeEventListener('hidden.bs.modal', once);
                    getProductModal().show();
                }, { once: true });
            }

            // Slight delay so Bootstrap finishes the previous transition
            setTimeout(() => {
                refreshStockList();
                getStockModal().show();
            }, wasOpen ? 200 : 0);
        }

        function refreshStockList() {
            const params = new URLSearchParams({ product_id: stockCtx.productId, variant_idx: stockCtx.variantIdx });
            fetch('?action=adminStockList&' + params, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => {
                    const list = document.getElementById('stock-list');
                    const badge = document.getElementById('stock-available-badge');
                    badge.textContent = (d.available || 0) + ' còn hàng';
                    if (!d.items || d.items.length === 0) {
                        list.innerHTML = '<div class="text-center text-muted py-5"><i class="fa-solid fa-box-open fs-3 opacity-25 d-block mb-2"></i>Chưa có hàng trong kho.</div>';
                        return;
                    }
                    list.innerHTML = d.items.map(it => {
                        const isSold = it.status === 'sold';
                        const meta = isSold
                            ? `<span class="badge bg-secondary">Đã giao ${it.order_id ? '— ' + escHtml(it.order_id) : ''}</span>`
                            : `<span class="badge bg-success">Còn hàng</span>`;
                        const delBtn = isSold ? '' : `<button class="btn btn-sm text-danger border-0" onclick="deleteStockItem(${it.id})"><i class="fa-solid fa-trash"></i></button>`;
                        return `
                            <div class="d-flex align-items-start gap-2 p-2 border-bottom">
                                <div class="flex-grow-1">
                                    <pre class="small mb-1" style="white-space:pre-wrap;word-break:break-word;font-family:'Inter',monospace;">${escHtml(it.content)}</pre>
                                    <div>${meta}</div>
                                </div>
                                ${delBtn}
                            </div>`;
                    }).join('');
                });
        }

        function stockAdd() {
            const btn = document.getElementById('btnStockAdd');
            const lines = document.getElementById('stock-input-textarea').value;
            if (!lines.trim()) {
                AppNotify.warning('Hãy nhập ít nhất 1 dòng.', 'Nội dung trống');
                return;
            }
            if (!stockCtx.productId) {
                AppNotify.error('Không xác định được sản phẩm. Hãy lưu sản phẩm rồi mở lại.', 'Lỗi');
                return;
            }
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

            const fd = new FormData();
            fd.append('product_id', stockCtx.productId);
            fd.append('variant_idx', stockCtx.variantIdx);
            fd.append('lines', lines);

            apiPost('adminStockAdd', fd)
                .then(d => {
                    if (d.success) {
                        toastMsg('Đã nhập kho ' + d.added + ' đơn vị');
                        document.getElementById('stock-input-textarea').value = '';
                        refreshStockList();
                    } else {
                        AppNotify.error(d.message || 'Không thể thêm.', 'Lỗi nhập kho');
                    }
                })
                .catch(err => {
                    console.error('stockAdd error:', err);
                    AppNotify.error(err.message || 'Không thể kết nối server.', 'Lỗi mạng');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-plus me-1"></i> Thêm vào kho';
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('btnStockAdd');
            if (!btn) return;
            // Backup binding in case onclick gets stripped
            btn.onclick = btn.onclick || stockAdd;
        });

        function deleteStockItem(id) {
            Swal.fire({
                title: 'Xóa đơn vị này?',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#ef4444', confirmButtonText: 'Xóa'
            }).then(r => {
                if (!r.isConfirmed) return;
                apiPost('adminStockDelete', { id }).then(d => {
                    if (d.success) {
                        toastMsg('Đã xóa');
                        refreshStockList();
                    }
                });
            });
        }

        // ============== ADMIN ORDER MANAGEMENT ==============
        function handleOrderFilter() {
            ordersSearchQuery = (document.getElementById('order-search-input')?.value || '').trim();
            ordersStatusFilter = document.getElementById('order-filter-status')?.value || '';
            ordersCurrentPage = 1;
            fetchOrders(1);
        }

        function resetOrderFilter() {
            ordersSearchQuery = '';
            ordersStatusFilter = '';
            const sInput = document.getElementById('order-search-input');
            const stSelect = document.getElementById('order-filter-status');
            if (sInput) sInput.value = '';
            if (stSelect) stSelect.value = '';
            ordersCurrentPage = 1;
            fetchOrders(1);
        }

        function renderOrders() {
            const tbody = document.getElementById('order-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            const orders = APP_STATE.orders || [];

            if (orders.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="table-empty-state"><i class="fa-solid fa-receipt"></i><div>Không tìm thấy đơn hàng nào.</div></td></tr>`;
                updateOrdersPaginationUI();
                return;
            }

            orders.forEach(o => {
                let statusCls = o.status === 'completed' ? 'bg-success' 
                              : (o.status === 'processing' ? 'bg-primary' 
                              : (o.status === 'pending' ? 'bg-warning text-dark' : 'bg-danger'));
                let statusText = o.status === 'completed' ? 'Thành công' 
                               : (o.status === 'processing' ? 'Đang xử lý' 
                               : (o.status === 'pending' ? 'Chờ thanh toán' : 'Đã hủy'));

                const dateStr = o.created_at ? new Date(o.created_at.replace(' ', 'T')).toLocaleString('vi-VN') : '';
                const details = JSON.stringify(o).replace(/'/g, "&#39;");

                tbody.innerHTML += `
                    <tr>
                        <td><code class="fw-bold">#${escapeHtml(o.id)}</code></td>
                        <td>
                            <div class="fw-semibold text-dark">${escapeHtml(o.customer_email)}</div>
                            <div class="text-muted small">${escapeHtml(o.phone || '—')}</div>
                            ${o.contact_social ? `<div class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1 text-start" style="font-size:0.75rem;"><i class="fa-solid fa-paper-plane me-1"></i>${escapeHtml(o.contact_social)}</div>` : ''}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">${escapeHtml(o.product_name)}</div>
                            <div class="text-muted small">${escapeHtml(o.variant_name || '—')} (x${o.quantity})</div>
                        </td>
                        <td class="fw-bold">${formatCurrency(o.amount)}</td>
                        <td><span class="badge ${statusCls} rounded-pill">${statusText}</span></td>
                        <td class="small text-muted">${dateStr}</td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-dark" onclick='viewOrderDetails(${details})' title="Chi tiết"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-sm btn-outline-primary" onclick="openManualDeliver('${o.id}')" title="Giao hàng thủ công"><i class="fa-solid fa-truck"></i></button>
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Đổi trạng thái"><i class="fa-solid fa-tag"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item small" href="#" onclick="updateOrderStatus('${o.id}', 'pending')">Chờ thanh toán</a></li>
                                    <li><a class="dropdown-item small" href="#" onclick="updateOrderStatus('${o.id}', 'processing')">Đang xử lý</a></li>
                                    <li><a class="dropdown-item small" href="#" onclick="updateOrderStatus('${o.id}', 'completed')">Thành công</a></li>
                                    <li><a class="dropdown-item small" href="#" onclick="updateOrderStatus('${o.id}', 'cancelled')">Đã hủy</a></li>
                                </ul>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder('${o.id}')" title="Xóa đơn hàng"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            updateOrdersPaginationUI();
        }

        function fetchOrders(page) {
            ordersCurrentPage = page;
            const url = new URL(window.location.origin + window.location.pathname);
            url.searchParams.set('action', 'adminOrdersList');
            url.searchParams.set('page', page);
            if (ordersStatusFilter) {
                url.searchParams.set('status', ordersStatusFilter);
            }
            if (ordersSearchQuery) {
                url.searchParams.set('search', ordersSearchQuery);
            }

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    APP_STATE.orders = data.orders || [];
                    ordersCurrentPage = Number(data.currentPage || 1);
                    ordersTotalPages = Number(data.totalPages || 1);
                    ordersTotalCount = Number(data.totalOrders || 0);
                    renderOrders();
                } else {
                    AppNotify.error(data.message || 'Không thể tải danh sách đơn hàng.', 'Lỗi');
                }
            })
            .catch(() => AppNotify.error('Không thể tải danh sách đơn hàng.', 'Lỗi kết nối'))
            .finally(() => {
                updateOrdersPaginationUI();
            });
        }

        function updateOrdersPaginationUI() {
            const curPageEl = document.getElementById('orders-current-page');
            const totalPagesEl = document.getElementById('orders-total-pages');
            const totalCountEl = document.getElementById('orders-total-count');

            if (curPageEl) curPageEl.innerText = ordersCurrentPage;
            if (totalPagesEl) totalPagesEl.innerText = ordersTotalPages;
            if (totalCountEl) totalCountEl.innerText = ordersTotalCount;

            renderPaginationControls('orders-pagination-list', ordersCurrentPage, ordersTotalPages, 'fetchOrders');
        }

        function viewOrderDetails(o) {
            let itemsHtml = '';
            let delivered = [];
            try {
                delivered = typeof o.delivered_items === 'string' ? JSON.parse(o.delivered_items) : (o.delivered_items || []);
            } catch(e){}

            if (delivered && delivered.length > 0) {
                itemsHtml = `<div class="text-start mt-3"><label class="fw-bold text-dark small">Tài khoản đã giao:</label><pre class="bg-light p-2 border rounded-3 mt-1 small" style="white-space:pre-wrap;font-family:monospace;">${delivered.join('\n')}</pre></div>`;
            }

            let upgradeInfo = '';
            if (o.upgrade_email) {
                upgradeInfo = `
                    <div class="row text-start mt-2 border-top pt-2">
                        <div class="col-6"><strong>Email nâng cấp:</strong> ${o.upgrade_email}</div>
                        <div class="col-6"><strong>Mật khẩu:</strong> ${o.upgrade_pass || '—'}</div>
                        <div class="col-12 mt-1"><strong>Link liên hệ:</strong> ${o.upgrade_link || '—'}</div>
                    </div>
                `;
            }

            Swal.fire({
                title: 'Chi tiết đơn hàng #' + o.id,
                html: `
                    <div class="text-start fs-6 text-muted">
                        <div class="row">
                            <div class="col-6 mb-2"><strong>Khách hàng:</strong> ${o.customer_email}</div>
                            <div class="col-6 mb-2"><strong>SĐT:</strong> ${o.phone || '—'}</div>
                            <div class="col-12 mb-2"><strong>Zalo / Telegram (gửi thủ công):</strong> <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6">${o.contact_social || '—'}</span></div>
                            <div class="col-12 mb-2"><strong>Sản phẩm:</strong> ${o.product_name} (${o.variant_name})</div>
                            <div class="col-6 mb-2"><strong>Số lượng:</strong> ${o.quantity}</div>
                            <div class="col-6 mb-2"><strong>Số tiền:</strong> ${formatCurrency(o.amount)}</div>
                            <div class="col-12 mb-2"><strong>Mã giao dịch:</strong> ${o.transaction_id || '—'}</div>
                            <div class="col-12 mb-2"><strong>Ghi chú:</strong> ${o.note || '—'}</div>
                        </div>
                        ${upgradeInfo}
                        ${itemsHtml}
                    </div>
                `,
                confirmButtonColor: '#111',
                confirmButtonText: 'Đóng',
                showCancelButton: true,
                cancelButtonColor: '#dc2626',
                cancelButtonText: '<i class="fa-solid fa-trash me-1"></i> Xóa đơn này'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    deleteOrder(o.id);
                }
            });
        }

        let manualDeliverModal;
        function getManualDeliverModal() { return manualDeliverModal ||= new bootstrap.Modal(document.getElementById('manualDeliverModal')); }

        function openManualDeliver(orderId) {
            const order = (APP_STATE.orders || []).find(o => o.id == orderId);
            const customerEmail = order ? order.customer_email : '';
            const productName = order ? order.product_name : '';
            
            document.getElementById('md_order_id').value = orderId;
            document.getElementById('md_lines').value = '';
            
            // Prefill email details
            document.getElementById('md_email_from').value = (APP_STATE.settings && APP_STATE.settings['smtp_from_email']) || '';
            document.getElementById('md_email_to').value = customerEmail;
            
            // Generate subject & body using templates from settings
            let subjectTpl = (APP_STATE.settings && APP_STATE.settings['smtp_default_subject']) || 'Bàn giao tài khoản / Key dịch vụ đơn hàng #{order_id}';
            let bodyTpl = (APP_STATE.settings && APP_STATE.settings['smtp_default_body']) || "Chào bạn,\n\nĐây là thông tin tài khoản / key kích hoạt cho đơn hàng #{order_id} ({product_name}) của bạn:\n\n{delivered_accounts}\n\nCảm ơn bạn đã tin dùng dịch vụ của chúng tôi!\nNếu có bất kỳ câu hỏi nào, vui lòng liên hệ hỗ trợ.\nTrân trọng,\nBan quản trị.";
            
            subjectTpl = subjectTpl.replace(/#{order_id}/g, orderId).replace(/{product_name}/g, productName);
            document.getElementById('md_email_subject').value = subjectTpl;
            
            const updateEmailBody = () => {
                const lines = document.getElementById('md_lines').value;
                let currentBody = bodyTpl.replace(/#{order_id}/g, orderId)
                                         .replace(/{product_name}/g, productName)
                                         .replace(/{delivered_accounts}/g, lines || '(Chưa nhập tài khoản)');
                document.getElementById('md_email_body').value = currentBody;
            };
            
            document.getElementById('md_lines').oninput = updateEmailBody;
            updateEmailBody(); // Initial update
            
            // Reset modal states
            document.getElementById('md_send_email').checked = false;
            document.getElementById('md_email_section').style.display = 'none';
            document.querySelector('#manualDeliverModal .modal-dialog').classList.remove('modal-lg');
            
            document.getElementById('md_email_img1').value = '';
            document.getElementById('md_email_img2').value = '';
            
            getManualDeliverModal().show();
        }

        function toggleMdEmailSection() {
            const sendEmail = document.getElementById('md_send_email').checked;
            const emailSection = document.getElementById('md_email_section');
            const modalDialog = document.querySelector('#manualDeliverModal .modal-dialog');
            
            if (sendEmail) {
                emailSection.style.display = 'block';
                modalDialog.classList.add('modal-lg');
            } else {
                emailSection.style.display = 'none';
                modalDialog.classList.remove('modal-lg');
            }
        }

        function submitManualDeliver() {
            const orderId = document.getElementById('md_order_id').value;
            const lines = document.getElementById('md_lines').value;
            if (!lines.trim()) {
                AppNotify.warning('Vui lòng nhập thông tin bàn giao.', 'Trống');
                return;
            }

            const sendEmail = document.getElementById('md_send_email').checked ? '1' : '0';

            const fd = new FormData();
            fd.append('id', orderId);
            fd.append('lines', lines);
            fd.append('send_email', sendEmail);
            fd.append('csrf_token', APP_STATE.csrfToken);

            if (sendEmail === '1') {
                fd.append('email_from', document.getElementById('md_email_from').value);
                fd.append('email_to', document.getElementById('md_email_to').value);
                fd.append('email_subject', document.getElementById('md_email_subject').value);
                fd.append('email_body', document.getElementById('md_email_body').value);
                
                const img1 = document.getElementById('md_email_img1').files[0];
                if (img1) fd.append('email_img1', img1);
                
                const img2 = document.getElementById('md_email_img2').files[0];
                if (img2) fd.append('email_img2', img2);
            }

            const btn = document.querySelector('#manualDeliverModal .btn-black');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

            fetch('?action=adminUpdateOrderDelivery', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: fd,
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.warning) {
                        Swal.fire({
                            title: 'Bàn giao thành công',
                            text: data.warning,
                            icon: 'warning',
                            confirmButtonColor: '#111',
                            confirmButtonText: 'Đóng'
                        }).then(() => {
                            getManualDeliverModal().hide();
                            location.reload();
                        });
                    } else {
                        AppNotify.success('Đã giao hàng và chuyển trạng thái đơn hàng sang Thành công!');
                        getManualDeliverModal().hide();
                        location.reload();
                    }
                } else {
                    AppNotify.error(data.message || 'Lỗi bàn giao');
                }
            })
            .catch(() => AppNotify.error('Không thể kết nối server.'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        function updateOrderStatus(orderId, status) {
            Swal.fire({
                title: 'Cập nhật trạng thái?',
                text: `Xác nhận đổi đơn hàng sang trạng thái này?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#111',
                confirmButtonText: 'Đồng ý'
            }).then((result) => {
                if (result.isConfirmed) {
                    const fd = new FormData();
                    fd.append('id', orderId);
                    fd.append('status', status);
                    fd.append('csrf_token', APP_STATE.csrfToken);

                    fetch('?action=adminUpdateOrderStatus', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                        body: fd,
                        credentials: 'same-origin'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            AppNotify.success('Cập nhật trạng thái thành công!');
                            location.reload();
                        } else {
                            AppNotify.error(data.message || 'Lỗi cập nhật');
                        }
                    })
                    .catch(() => AppNotify.error('Không thể kết nối server.'));
                }
            });
        }

        function deleteOrder(orderId) {
            Swal.fire({
                title: 'Xóa đơn hàng?',
                html: `Bạn có chắc chắn muốn xóa đơn hàng <b>#${orderId}</b>?<br><small class="text-danger">Dữ liệu đơn hàng này sẽ bị xóa vĩnh viễn khỏi hệ thống.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Xóa đơn',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    const fd = new FormData();
                    fd.append('id', orderId);
                    fd.append('csrf_token', APP_STATE.csrfToken);

                    fetch('?action=adminDeleteOrder', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                        body: fd,
                        credentials: 'same-origin'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            AppNotify.success('Đã xóa đơn hàng thành công!');
                            if (typeof fetchOrders === 'function') {
                                fetchOrders(ordersCurrentPage);
                            } else {
                                location.reload();
                            }
                        } else {
                            AppNotify.error(data.message || 'Không thể xóa đơn hàng.');
                        }
                    })
                    .catch(() => AppNotify.error('Không thể kết nối máy chủ.'));
                }
            });
        }
    </script>

    <!-- Modals for User, Blog and Orders -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold">Sửa user</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="user_id">
                    <div class="mb-3">
                        <label class="form-label">Tên</label>
                        <input type="text" class="form-control" id="user_name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="user_email">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quyền</label>
                            <select class="form-select" id="user_role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" id="user_status">
                                <option value="active">Active</option>
                                <option value="blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-black px-4" onclick="saveUser()">Lưu user</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resetPassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold">Mật khẩu mới</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">User: <span class="fw-semibold text-dark" id="reset_pass_email"></span></p>
                    <label class="form-label">Copy mật khẩu này gửi cho user</label>
                    <div class="input-group">
                        <input type="text" class="form-control fw-bold" id="reset_pass_value" readonly>
                        <button class="btn btn-black" type="button" onclick="copyResetPassword()">
                            <i class="fa-regular fa-copy me-1"></i>Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="blogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold">Quản lý bài viết</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="blogForm" enctype="multipart/form-data">
                        <input type="hidden" id="blog_id">
                        <input type="hidden" id="blog_image_url">

                        <div class="mb-3">
                            <label class="form-label">Tiêu đề bài viết</label>
                            <input type="text" class="form-control" id="blog_title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh đại diện</label>
                            <div class="d-flex align-items-start gap-3">
                                <div class="blog-image-preview-wrap">
                                    <img id="blog_image_preview" src="" alt="" class="blog-image-preview" style="display:none;">
                                    <div id="blog_image_placeholder" class="blog-image-placeholder">
                                        <i class="fa-regular fa-image"></i>
                                        <span>Chưa có ảnh</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control form-control-sm" id="blog_image_file" accept="image/png,image/jpeg,image/webp,image/gif">
                                    <small class="text-muted d-block mt-1">Định dạng: JPG, PNG, WEBP, GIF. Tối đa 10MB.</small>
                                    <button type="button" class="btn btn-sm btn-link text-danger px-0 mt-1 d-none" id="blog_image_clear">
                                        <i class="fa-solid fa-xmark me-1"></i>Bỏ ảnh
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả ngắn</label>
                            <textarea id="blog_desc" class="form-control" rows="3" maxlength="220" placeholder="Tóm tắt 1-2 câu có từ khóa chính, hiển thị ở Google và danh sách bài viết."></textarea>
                            <small class="text-muted d-block mt-1">Nên viết 80-180 ký tự, rõ nội dung chính của bài.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nội dung chi tiết bài viết</label>
                            <div class="rich-toolbar btn-group flex-wrap mb-1" role="toolbar">
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="bold" title="Đậm"><i class="fa-solid fa-bold"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="italic" title="Nghiêng"><i class="fa-solid fa-italic"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="underline" title="Gạch dưới"><i class="fa-solid fa-underline"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="strikeThrough" title="Gạch ngang"><i class="fa-solid fa-strikethrough"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="formatBlock" data-arg="H2" title="Tiêu đề lớn">H2</button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="formatBlock" data-arg="H3" title="Tiêu đề nhỏ">H3</button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="formatBlock" data-arg="P" title="Văn bản">P</button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="insertUnorderedList" title="Danh sách"><i class="fa-solid fa-list-ul"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="insertOrderedList" title="Danh sách số"><i class="fa-solid fa-list-ol"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="formatBlock" data-arg="BLOCKQUOTE" title="Trích dẫn"><i class="fa-solid fa-quote-right"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="createLink" title="Chèn liên kết"><i class="fa-solid fa-link"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" data-cmd="removeFormat" title="Xoá định dạng"><i class="fa-solid fa-eraser"></i></button>
                            </div>
                            <div id="blog_content_editor" class="rich-editor" contenteditable="true"></div>
                            <textarea id="blog_content" class="d-none"></textarea>
                            <small class="text-muted d-block mt-1">Mẹo: bôi đen text rồi chọn nút trên thanh công cụ để định dạng.</small>
                        </div>

                        <div class="mt-4 border-top pt-3">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-magnifying-glass me-1"></i> Cấu hình SEO tối ưu Google</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SEO Slug (Đường dẫn thân thiện)</label>
                                    <input type="text" class="form-control" id="blog_seo_slug" placeholder="VD: huong-dan-dang-ky-claude">
                                    <small class="text-muted">Để trống để tự động tạo từ tiêu đề.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SEO Title (Tiêu đề Google)</label>
                                    <input type="text" class="form-control" id="blog_seo_title" placeholder="Tiêu đề hiển thị trên Google">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">SEO Description (Mô tả Google)</label>
                                    <textarea class="form-control" id="blog_seo_description" rows="2" placeholder="Mô tả tóm tắt bài viết hiển thị trên Google"></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">SEO Keywords (Từ khóa Google)</label>
                                    <input type="text" class="form-control" id="blog_seo_keywords" placeholder="Cách nhau bằng dấu phẩy">
                                </div>
                                <div class="col-12">
                                    <div class="seo-quality-box">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-semibold text-dark"><i class="fa-solid fa-list-check me-1"></i> Kiểm tra chuẩn SEO</div>
                                            <span class="badge bg-danger" id="blog_seo_score">0/7</span>
                                        </div>
                                        <ul class="seo-quality-list" id="blog_seo_checklist"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-black px-4" onclick="saveBlog()">Lưu bài viết</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Stock manager modal -->
    <div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Quản lý kho</h5>
                        <small class="text-muted" id="stock-modal-subtitle"></small>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border small mb-3">
                        <i class="fa-solid fa-circle-info text-primary me-1"></i>
                        Mỗi <strong>dòng</strong> là 1 đơn vị bán. Khách mua N → hệ thống tự lấy N dòng đầu, đánh dấu đã giao. Hỗ trợ định dạng nhiều dòng cho 1 unit bằng cách thêm dòng trống giữa các unit.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nhập kho mới (mỗi dòng 1 đơn vị)</label>
                        <textarea class="form-control" id="stock-input-textarea" rows="6" placeholder="account1@gmail.com|password1&#10;account2@gmail.com|password2&#10;account3@gmail.com|password3"></textarea>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="button" class="btn btn-black px-4" id="btnStockAdd" onclick="stockAdd()"><i class="fa-solid fa-plus me-1"></i> Thêm vào kho</button>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0">Kho hiện tại</h6>
                        <span class="badge bg-success" id="stock-available-badge">0 còn hàng</span>
                    </div>
                    <div id="stock-list" class="border rounded-3" style="max-height:340px;overflow-y:auto;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold" id="catModalTitle">Thêm Danh mục mới</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="categoryForm">
                        <input type="hidden" id="cat_id">
                        <div class="mb-3">
                            <label class="form-label">Tên hiển thị</label>
                            <input type="text" class="form-control" id="cat_name" placeholder="Ví dụ: ChatGPT Plus"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug lọc (Dùng để nhóm sản phẩm)</label>
                            <input type="text" class="form-control" id="cat_slug" placeholder="Ví dụ: chatgpt" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hiệu ứng PRO (Glow)</label>
                            <select class="form-select" id="cat_is_pro">
                                <option value="0">Không</option>
                                <option value="1">Có (Hiệu ứng viền sáng)</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Icon (FontAwesome)</label>
                                <input type="text" class="form-control" id="cat_icon" placeholder="fa-wand-magic-sparkles">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Màu Icon (Class)</label>
                                <input type="text" class="form-control" id="cat_icon_color" placeholder="text-primary">
                            </div>
                        </div>

                        <div class="mt-4 border-top pt-3">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-magnifying-glass me-1"></i> Cấu hình SEO tối ưu Google</h6>
                            <div class="mb-3">
                                <label class="form-label">SEO Slug (Đường dẫn thân thiện)</label>
                                <input type="text" class="form-control" id="cat_seo_slug" placeholder="VD: tai-khoan-chatgpt">
                                <small class="text-muted">Để trống để lấy theo slug lọc.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SEO Title (Tiêu đề Google)</label>
                                <input type="text" class="form-control" id="cat_seo_title" placeholder="Tiêu đề hiển thị trên Google">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SEO Description (Mô tả Google)</label>
                                <textarea class="form-control" id="cat_seo_description" rows="2" placeholder="Mô tả danh mục hiển thị trên Google"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SEO Keywords (Từ khóa Google)</label>
                                <input type="text" class="form-control" id="cat_seo_keywords" placeholder="Cách nhau bằng dấu phẩy">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-black px-4" onclick="saveCategory()">Lưu danh mục</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Deliver Modal -->
    <div class="modal fade" id="manualDeliverModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold">Giao hàng thủ công</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="md_order_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nhập tài khoản bàn giao (mỗi dòng 1 tài khoản)</label>
                        <textarea class="form-control" id="md_lines" rows="4" placeholder="account1@gmail.com|password1&#10;account2@gmail.com|password2"></textarea>
                        <small class="text-muted d-block mt-1">Hệ thống sẽ lưu thông tin bàn giao vào đơn hàng và cập nhật trạng thái đơn thành <strong>Thành công (Completed)</strong>.</small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="md_send_email" onchange="toggleMdEmailSection()">
                        <label class="form-check-label fw-bold small text-primary" for="md_send_email">
                            <i class="fa-solid fa-envelope me-1"></i> Gửi KEY / Tài Khoản qua Email
                        </label>
                    </div>

                    <div id="md_email_section" style="display: none;" class="p-3 bg-light rounded-3 border mb-3">
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold small">Từ (From Email)</label>
                                <input type="email" class="form-control form-control-sm" id="md_email_from" placeholder="no-reply@aicuatoi.net">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold small">Đến (To Email)</label>
                                <input type="email" class="form-control form-control-sm" id="md_email_to" placeholder="khachhang@gmail.com">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small">Tiêu đề (Subject)</label>
                            <input type="text" class="form-control form-control-sm" id="md_email_subject">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small">Nội dung Email (Sửa nếu cần)</label>
                            <textarea class="form-control form-control-sm" id="md_email_body" rows="6"></textarea>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small"><i class="fa-solid fa-image text-muted me-1"></i>Hình ảnh 1 (Đính kèm)</label>
                                <input type="file" class="form-control form-control-sm" id="md_email_img1" accept="image/*">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small"><i class="fa-solid fa-image text-muted me-1"></i>Hình ảnh 2 (Đính kèm)</label>
                                <input type="file" class="form-control form-control-sm" id="md_email_img2" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-black px-4" onclick="submitManualDeliver()">Gửi bàn giao</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Keyword CRUD Modal -->
    <div class="modal fade" id="keywordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold" id="keywordModalTitle">Thêm từ khóa mới</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="keywordForm">
                        <input type="hidden" id="kw_old_slug">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Từ khóa Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kw_slug" placeholder="Ví dụ: chatgpt" required>
                                <small class="text-muted">Chỉ chứa chữ thường không dấu, số và dấu gạch ngang.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên hiển thị <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kw_display_name" placeholder="Ví dụ: ChatGPT Plus" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">SEO Title</label>
                                <input type="text" class="form-control" id="kw_title" placeholder="Tiêu đề hiển thị trên Google">
                                <small class="text-muted">Để trống hệ thống tự tạo mẫu chuẩn SEO.</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">SEO Description</label>
                                <textarea class="form-control" id="kw_description" rows="2" placeholder="Mô tả khi tìm kiếm trên Google"></textarea>
                                <small class="text-muted">Để trống hệ thống tự tạo mô tả mẫu.</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">SEO Keywords (cách nhau bằng dấu phẩy)</label>
                                <input type="text" class="form-control" id="kw_keywords" placeholder="tai khoan chatgpt, chatgpt gia re, mua chatgpt">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Aliases / Từ đồng nghĩa (cách nhau bằng dấu phẩy)</label>
                                <input type="text" class="form-control" id="kw_aliases" placeholder="gpt, chat-gpt, open-ai, chatgpt-plus">
                                <small class="text-muted">Các slug phụ tự động redirect 301 về slug chính này.</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-black px-4" onclick="saveKeyword()">Lưu từ khóa</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bulk Keyword Import Modal -->
    <div class="modal fade" id="bulkKeywordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold">Nhập từ khóa hàng loạt</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border small mb-3">
                        <i class="fa-solid fa-circle-info text-primary me-1"></i>
                        Định dạng mỗi dòng: <code>slug|tên hiển thị|aliases (nếu có)</code>. Aliases cách nhau bằng dấu phẩy.<br>
                        Ví dụ:<br>
                        <code>chatgpt|ChatGPT Plus|gpt,chat-gpt</code><br>
                        <code>gemini|Gemini Advanced|google-gemini</code>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nhập danh sách từ khóa</label>
                        <textarea class="form-control" id="bulk-keywords-input" rows="8" placeholder="chatgpt|ChatGPT Plus|gpt,chat-gpt&#10;gemini|Gemini Advanced|google-gemini"></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="bulk-keywords-replace-all">
                        <label class="form-check-label text-danger small fw-bold" for="bulk-keywords-replace-all">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Xóa sạch toàn bộ từ khóa hiện có trên hệ thống trước khi nhập mới
                        </label>
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-outline-dark btn-sm px-4" onclick="previewBulkKeywords()">
                            <i class="fa-solid fa-eye me-1"></i> Xem trước (Review)
                        </button>
                    </div>

                    <div id="bulk-keywords-preview-area" class="d-none">
                        <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-magnifying-glass me-1"></i> Kết quả phân tích (Xem trước)</h6>
                        <div class="table-responsive border rounded-3" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-custom mb-0" style="font-size: 0.82rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Slug</th>
                                        <th>Tên hiển thị</th>
                                        <th>SEO Title (Tự tạo)</th>
                                        <th>Aliases</th>
                                    </tr>
                                </thead>
                                <tbody id="bulk-keywords-preview-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success px-4" id="btnBulkKeywordsConfirm" onclick="confirmBulkKeywords()" disabled>Xác nhận thêm hàng loạt</button>
                </div>
            </div>
        </div>
    </div>

<script>
window.AdminChat = (function() {
    let activeSessionId = null;
    let conversations = [];
    let pollTimer = null;

    function init() {
        loadConversations();
        startPolling(15000);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                startPolling(60000);
            } else {
                loadConversations();
                startPolling(15000);
            }
        });
    }

    function startPolling(ms) {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(loadConversations, ms);
    }

    let lastUnreadTotal = -1;

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

    function loadConversations() {
        fetch('?action=adminChatGetConversations')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    conversations = data.conversations || [];
                    renderConversationsList();
                    const unread = Number(data.unread_total || 0);
                    updateBadge(unread);

                    if (lastUnreadTotal >= 0 && unread > lastUnreadTotal) {
                        playNotificationSound();
                    }
                    lastUnreadTotal = unread;

                    if (activeSessionId) {
                        loadMessages(activeSessionId, false);
                    }
                }
            })
            .catch(() => {});
    }

    function updateBadge(count) {
        const badge = document.getElementById('chat-unread-badge-sidebar');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
                badge.textContent = '0';
            }
        }
    }

    function renderConversationsList() {
        const container = document.getElementById('admin-conversations-list');
        if (!container) return;

        const searchKeyword = (document.getElementById('admin-chat-search')?.value || '').toLowerCase().trim();
        const filtered = conversations.filter(c => {
            const name = (c.sender_name || 'Khách').toLowerCase();
            const sid = (c.session_id || '').toLowerCase();
            const msg = (c.last_message || '').toLowerCase();
            return name.includes(searchKeyword) || sid.includes(searchKeyword) || msg.includes(searchKeyword);
        });

        if (filtered.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-muted small">Không có cuộc hội thoại nào.</div>';
            return;
        }

        let html = '';
        filtered.forEach(c => {
            const isActive = c.session_id === activeSessionId;
            const unread = Number(c.unread_count || 0);
            const timeStr = c.last_time ? c.last_time.substring(11, 16) : '';
            const isUser = c.last_sender_type === 'user';
            const prefix = isUser ? '' : 'Admin: ';

            html += `
            <div class="p-3 border-bottom conversation-item ${isActive ? 'bg-light border-start border-4 border-dark fw-semibold' : ''}" 
                 style="cursor: pointer; transition: all 0.2s;" 
                 onclick="AdminChat.selectConversation('${c.session_id}', '${escapeHtml(c.sender_name || 'Khách')}')">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark text-truncate" style="max-width: 130px;">${escapeHtml(c.sender_name || 'Khách')}</span>
                        ${c.user_id ? '<span class="badge bg-dark" style="font-size:0.65rem;">User</span>' : '<span class="badge bg-secondary" style="font-size:0.65rem;">Guest</span>'}
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">${timeStr}</small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0 text-muted small text-truncate" style="max-width: 180px;">${prefix}${escapeHtml(c.last_message || '')}</p>
                    ${unread > 0 ? `<span class="badge bg-danger rounded-pill">${unread}</span>` : ''}
                </div>
            </div>`;
        });
        container.innerHTML = html;
    }

    function showMobileList() {
        const listCol = document.getElementById('admin-chat-col-list');
        const detailCol = document.getElementById('admin-chat-col-detail');
        if (listCol && detailCol) {
            listCol.classList.remove('d-none');
            listCol.classList.add('d-flex');
            detailCol.classList.remove('d-flex');
            detailCol.classList.add('d-none');
        }
    }

    function selectConversation(sessionId, senderName) {
        activeSessionId = sessionId;
        document.getElementById('admin-chat-user-name').textContent = senderName;
        document.getElementById('admin-chat-user-sub').textContent = 'ID: ' + sessionId;
        document.getElementById('admin-chat-input-area').style.display = 'block';

        const listCol = document.getElementById('admin-chat-col-list');
        const detailCol = document.getElementById('admin-chat-col-detail');
        if (listCol && detailCol && window.innerWidth < 768) {
            listCol.classList.remove('d-flex');
            listCol.classList.add('d-none');
            detailCol.classList.remove('d-none');
            detailCol.classList.add('d-flex');
        }

        renderConversationsList();
        loadMessages(sessionId, true);
    }

    function loadMessages(sessionId, scroll = true) {
        fetch(`?action=adminChatGetMessages&session_id=${sessionId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.session_id === activeSessionId) {
                    renderMessages(data.messages || []);
                    if (scroll) scrollToBottom();
                }
            })
            .catch(() => {});
    }

    function formatMsgContent(text) {
        if (!text) return '';
        const trimmed = text.trim();
        if (trimmed.startsWith('[img]') && trimmed.endsWith('[/img]')) {
            const url = trimmed.substring(5, trimmed.length - 6);
            return `<a href="${escapeHtml(url)}" target="_blank" class="d-inline-block mt-1"><img src="${escapeHtml(url)}" alt="Hình ảnh" style="max-width:260px; max-height:260px; border-radius:10px; border:1px solid #cbd5e1; object-fit:cover;"></a>`;
        }
        if (/^https?:\/\/.+\.(png|jpg|jpeg|gif|webp)(\?.*)?$/i.test(trimmed)) {
            return `<a href="${escapeHtml(trimmed)}" target="_blank" class="d-inline-block mt-1"><img src="${escapeHtml(trimmed)}" alt="Hình ảnh" style="max-width:260px; max-height:260px; border-radius:10px; border:1px solid #cbd5e1; object-fit:cover;"></a>`;
        }
        return escapeHtml(text);
    }

    let lastAdminMessagesFingerprint = '';

    function renderMessages(messages, forceScroll = false) {
        const container = document.getElementById('admin-messages-container');
        if (!container) return;

        const newFingerprint = (messages || []).map(m => `${m.id}_${m.is_read}`).join('|');
        if (newFingerprint === lastAdminMessagesFingerprint && !forceScroll) {
            return;
        }

        const isNearBottom = (container.scrollHeight - container.scrollTop <= container.clientHeight + 80);

        let html = '';
        (messages || []).forEach(msg => {
            const isAdmin = msg.sender_type === 'admin';
            const timeStr = msg.created_at ? msg.created_at.substring(11, 16) : '';
            const contentHtml = formatMsgContent(msg.message);

            if (isAdmin) {
                html += `
                <div class="d-flex justify-content-end mb-3">
                    <div class="text-end" style="max-width: 75%;">
                        <div class="bg-dark text-white p-3 rounded-3 shadow-sm text-start" style="border-top-right-radius: 2px !important;">
                            ${contentHtml}
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">${escapeHtml(msg.sender_name || 'Admin')} • ${timeStr}</small>
                    </div>
                </div>`;
            } else {
                html += `
                <div class="d-flex justify-content-start mb-3">
                    <div class="text-start" style="max-width: 75%;">
                        <div class="bg-white text-dark p-3 rounded-3 border shadow-sm" style="border-top-left-radius: 2px !important;">
                            ${contentHtml}
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">${escapeHtml(msg.sender_name || 'Khách')} • ${timeStr}</small>
                    </div>
                </div>`;
            }
        });

        container.innerHTML = html;
        lastAdminMessagesFingerprint = newFingerprint;

        if (forceScroll || isNearBottom) {
            requestAnimationFrame(() => {
                container.scrollTop = container.scrollHeight;
            });
        }
    }

    function scrollToBottom() {
        const container = document.getElementById('admin-messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function sendMessage(e) {
        if (e) e.preventDefault();
        if (!activeSessionId) return;

        const input = document.getElementById('admin-chat-input');
        const text = input ? input.value.trim() : '';
        if (!text) return;

        input.value = '';

        fetch('?action=adminChatSendMessage', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': APP_STATE.csrfToken
            },
            body: JSON.stringify({
                session_id: activeSessionId,
                message: text
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderMessages(data.messages || []);
                scrollToBottom();
                loadConversations();
            } else {
                AppNotify.error(data.message || 'Không thể gửi tin nhắn.');
            }
        })
        .catch(() => AppNotify.error('Không thể kết nối server.'));
    }

    function handleImageUpload(e) {
        const file = e.target.files?.[0];
        if (!file || !activeSessionId) return;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('session_id', activeSessionId);
        formData.append('csrf_token', APP_STATE.csrfToken);

        fetch('?action=adminChatUploadImage', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': APP_STATE.csrfToken
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderMessages(data.messages || []);
                scrollToBottom();
                loadConversations();
            } else {
                AppNotify.error(data.message || 'Không thể tải hình ảnh lên.');
            }
        })
        .catch(() => AppNotify.error('Không thể kết nối server.'))
        .finally(() => {
            e.target.value = '';
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    document.addEventListener('DOMContentLoaded', init);

    return {
        selectConversation,
        sendMessage,
        handleImageUpload,
        showMobileList,
        filterConversations: renderConversationsList
    };
})();
</script>
</body>

</html>
