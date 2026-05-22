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
            overflow-x: hidden;
        }

        /* ================= LAYOUT ================= */
        .admin-wrapper {
            display: flex;
            height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--pure-black);
            color: var(--pure-white);
            display: flex;
            flex-direction: column;
            transition: 0.3s;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 20px;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            border-bottom: 1px solid #333;
            letter-spacing: -1px;
        }

        .nav-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
            flex-grow: 1;
        }

        .nav-item {
            padding: 0 15px;
            margin-bottom: 5px;
        }

        .nav-link {
            color: #a1a1aa;
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-right: 10px;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #27272a;
            color: var(--pure-white);
        }

        /* MAIN CONTENT */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .topbar {
            background: var(--pure-white);
            height: 70px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-area {
            padding: 30px;
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

        /* ===== ADMIN CHAT ===== */
        .chat-thread-item:hover { background: #f9fafb; cursor: pointer; }
        .chat-thread-item.active { background: #f0f0f0; }

        .chat-textarea {
            resize: none;
            line-height: 1.4;
            min-height: 38px;
            max-height: 140px;
            padding: 8px 12px;
            border-radius: 18px;
            border: 1px solid var(--border-color);
        }

        #chat-messages { display: flex; flex-direction: column; gap: 8px; }
        #chat-messages .chat-msg {
            display: flex;
            flex-direction: column;
            max-width: 75%;
            min-width: 0;
        }
        #chat-messages .chat-msg .bubble {
            padding: 8px 12px;
            border-radius: 14px;
            font-size: 0.92rem;
            line-height: 1.45;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: pre-wrap;
        }
        #chat-messages .chat-msg .meta {
            font-size: 0.7rem;
            color: #888;
            margin-top: 2px;
        }
        #chat-messages .chat-msg.msg-me {
            align-self: flex-end;
            align-items: flex-end;
        }
        #chat-messages .chat-msg.msg-me .bubble {
            background: var(--pure-black);
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        #chat-messages .chat-msg.msg-them {
            align-self: flex-start;
            align-items: flex-start;
        }
        #chat-messages .chat-msg.msg-them .bubble {
            background: #fff;
            color: #111;
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 4px;
        }

        /* Attachments */
        #chat-messages .bubble-att {
            padding: 4px !important;
            background: transparent !important;
            border: none !important;
            max-width: 100%;
        }
        #chat-messages .chat-att-img {
            display: inline-block;
            max-width: 100%;
        }
        #chat-messages .chat-att-img img {
            max-width: 240px;
            max-height: 240px;
            width: auto;
            height: auto;
            border-radius: 12px;
            display: block;
            object-fit: cover;
            cursor: zoom-in;
        }
        #chat-messages .chat-att-file {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid var(--border-color);
            color: var(--pure-black);
            text-decoration: none;
            max-width: 100%;
        }
        #chat-messages .chat-msg.msg-me .chat-att-file {
            background: #1f1f1f;
            color: #fff;
            border-color: #1f1f1f;
        }
        #chat-messages .chat-msg.msg-me .chat-att-file .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        #chat-messages .chat-att-file:hover { background: #f4f4f4; }
        #chat-messages .chat-msg.msg-me .chat-att-file:hover { background: #2a2a2a; }

        #chat-reply-preview {
            font-size: 0.85rem;
        }
        #chat-reply-preview img {
            max-width: 40px !important;
            max-height: 40px !important;
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

        .rich-toolbar { gap: 4px; }
        .rich-toolbar .btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0;
            font-weight: 600;
        }
        .rich-toolbar .btn[data-arg^="H"] { width: auto; padding: 0 8px; }
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
        .rich-editor h2 { font-size: 1.4rem; font-weight: 700; margin: 12px 0 6px; }
        .rich-editor h3 { font-size: 1.15rem; font-weight: 700; margin: 10px 0 6px; }
        .rich-editor blockquote {
            border-left: 3px solid #111;
            padding: 4px 12px;
            color: #555;
            font-style: italic;
            margin: 8px 0;
        }
        .rich-editor ul, .rich-editor ol { padding-left: 22px; }
    </style>
</head>

<body>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-circle-nodes me-2"></i>AI CỦA TÔI
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="switchView('dashboard', this)">
                        <i class="fa-solid fa-chart-pie"></i> Tổng quan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active" onclick="switchView('products', this)">
                        <i class="fa-solid fa-box"></i> Quản lý Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="switchView('categories', this)">
                        <i class="fa-solid fa-list-ul"></i> Quản lý Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="switchView('blogs', this)">
                        <i class="fa-solid fa-newspaper"></i> Quản lý Tin tức
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="switchView('settings', this)">
                        <i class="fa-solid fa-gear"></i> Cấu hình Website
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link position-relative" onclick="switchView('chat', this); loadChatThreads();">
                        <i class="fa-solid fa-comments"></i> Hộp thư hỗ trợ
                        <span id="admin-chat-badge" class="badge bg-danger rounded-pill ms-auto d-none">0</span>
                    </a>
                </li>
            </ul>
            <div class="p-3 border-top" style="border-color: #333 !important;">
                <div class="d-flex align-items-center text-white">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=fff&color=000"
                        class="rounded-circle me-2" width="35">
                    <div style="font-size: 0.85rem;">
                        <div class="fw-bold"><?php echo htmlspecialchars($currentUser['name'] ?? 'Admin'); ?></div>
                        <div class="text-secondary">
                            <?php echo htmlspecialchars($currentUser['email'] ?? 'admin@aicualtoi.com'); ?></div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h5 class="mb-0 fw-bold" id="page-title">Quản lý Sản phẩm</h5>
                <div>
                    <button class="btn btn-light border-0 shadow-sm me-2"><i class="fa-regular fa-bell"></i></button>
                    <a href="index.php?action=logout" class="btn btn-light border-0 shadow-sm me-2"><i
                            class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</a>
                    <a href="index.php" target="_blank" class="btn btn-black"><i
                            class="fa-solid fa-arrow-up-right-from-square me-2"></i>Xem Website</a>
                </div>
            </header>

            <div class="content-area">

                <div id="view-products" class="view-section active">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <div>
                                <h6 class="mb-0 fw-bold">Danh sách Dịch vụ / Sản phẩm</h6>
                                <small class="text-muted">Quản lý các sản phẩm hiển thị trên trang chủ</small>
                            </div>
                            <button class="btn btn-black" onclick="openProductModal()">
                                <i class="fa-solid fa-plus me-1"></i> Thêm mới
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0">
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
                    </div>
                </div>

                <div id="view-categories" class="view-section">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <div>
                                <h6 class="mb-0 fw-bold">Quản lý Danh mục (Pill Menu)</h6>
                                <small class="text-muted">Cấu hình các nút lọc sản phẩm trên trang chủ</small>
                            </div>
                            <button class="btn btn-black" onclick="openCategoryModal()">
                                <i class="fa-solid fa-plus me-1"></i> Thêm danh mục
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0">
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

                <div id="view-blogs" class="view-section">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <div>
                                <h6 class="mb-0 fw-bold">Danh sách Tin tức</h6>
                                <small class="text-muted">Các bài viết hiển thị ở phần Tạp chí trên trang chủ</small>
                            </div>
                            <button class="btn btn-black" onclick="openBlogModal()">
                                <i class="fa-solid fa-plus me-1"></i> Viết bài mới
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0">
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
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-circle-info text-primary me-2"></i>
                                            <span class="small fw-bold">Webhook URL:</span>
                                            <code class="ms-2 text-danger"><?= url('index.php?action=sepayWebhook') ?></code>
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="loadSePayDebug()">
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
                </div>

                <div id="view-dashboard" class="view-section">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="card-custom p-4">
                                <div class="text-muted fw-bold mb-2">DOANH THU</div>
                                <h3 class="fw-bold text-dark">
                                    <?php
                                    $totalRevenue = array_reduce($orders, function ($carry, $item) {
                                        return $carry + ($item['status'] === 'completed' ? $item['amount'] : 0);
                                    }, 0);
                                    echo number_format($totalRevenue, 0, ',', '.') . 'đ';
                                    ?>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom p-4">
                                <div class="text-muted fw-bold mb-2">ĐƠN HÀNG MỚI</div>
                                <h3 class="fw-bold text-dark"><?php echo count($orders); ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom p-4">
                                <div class="text-muted fw-bold mb-2">KHÁCH HÀNG</div>
                                <h3 class="fw-bold text-dark">1,024</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom p-4">
                                <div class="text-muted fw-bold mb-2">TỔNG SẢN PHẨM</div>
                                <h3 class="fw-bold text-dark" id="dash-total-products"><?php echo count($products); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view-chat" class="view-section">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="card-custom" style="height: 70vh; display: flex; flex-direction: column;">
                                <div class="card-header-custom">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Khách hàng</h6>
                                        <small class="text-muted">Thread mới nhất ở trên cùng</small>
                                    </div>
                                    <button class="btn btn-light btn-sm border" onclick="loadChatThreads()" title="Làm mới">
                                        <i class="fa-solid fa-rotate"></i>
                                    </button>
                                </div>
                                <div id="chat-threads" class="overflow-auto flex-grow-1"></div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card-custom" style="height: 70vh; display: flex; flex-direction: column;">
                                <div class="card-header-custom">
                                    <div>
                                        <h6 class="mb-0 fw-bold" id="chat-active-name">Chọn một cuộc trò chuyện</h6>
                                        <small class="text-muted" id="chat-active-email"></small>
                                    </div>
                                </div>
                                <div id="chat-messages" class="flex-grow-1 p-3 overflow-auto" style="background: #f7f7f8;">
                                    <div class="text-center text-muted py-5">
                                        <i class="fa-regular fa-comment-dots fs-1 opacity-25 mb-3 d-block"></i>
                                        <p class="mb-0">Chưa có tin nhắn nào được mở.</p>
                                    </div>
                                </div>
                                <div id="chat-reply-preview" class="px-3 py-2 border-top d-none" style="background:#fff;"></div>
                                <div class="admin-chat-counter px-3 py-1 text-end text-muted" id="admin-chat-counter" style="font-size: 0.8rem; background: #f7f7f8; display: none; border-top: 1px solid var(--border-color);">0/700</div>
                                <form id="chat-reply-form" class="d-flex gap-2 p-3 border-top align-items-end" enctype="multipart/form-data" style="display:none !important;">
                                    <button type="button" class="btn btn-light border" id="btnAdminAttach" title="Đính kèm tệp"><i class="fa-solid fa-paperclip"></i></button>
                                    <input type="file" id="admin-chat-file" class="d-none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.txt,.csv">
                                    <textarea id="chat-reply-input" class="form-control chat-textarea" placeholder="Nhập câu trả lời... (Shift+Enter để xuống dòng)" maxlength="700" rows="1"></textarea>
                                    <button type="submit" class="btn btn-black px-3"><i class="fa-solid fa-paper-plane"></i></button>
                                </form>
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá bán (VNĐ)</label>
                                <input type="number" class="form-control" id="p_price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" id="p_status">
                                    <option value="active">Đang bán</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                    <option value="hidden">Ẩn</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Đường dẫn Hình ảnh (URL)</label>
                                <input type="url" class="form-control" id="p_image" placeholder="https://...">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Mô tả ngắn</label>
                                <input type="text" class="form-control" id="p_desc"
                                    placeholder="Ví dụ: Cấp tốc 5 phút, Bảo hành trọn đời...">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Mô tả chi tiết / Nội dung bộ công cụ</label>
                                <textarea class="form-control" id="p_detail_desc" rows="5"
                                    placeholder="Nhập nội dung hiển thị trong tab Mô tả chi tiết. Ví dụ: Bộ công cụ bao gồm..., lưu ý sử dụng, chính sách kèm theo..."></textarea>
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
                                    <div class="col-md-2 text-center">Up / Kho</div>
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

    <script>
        const APP_STATE = {
            categories: <?php echo json_encode($categories); ?>,
            settings: <?php echo json_encode($settings); ?>,
            products: <?php echo json_encode($products); ?>,
            blogs: <?php echo json_encode($blogs ?? []); ?>,
            csrfToken: <?php echo json_encode(Csrf::token()); ?>
        };

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

        let productModal, categoryModal, blogModal, stockModal;
        function getProductModal() { return productModal ||= new bootstrap.Modal(document.getElementById('productModal')); }
        function getCategoryModal() { return categoryModal ||= new bootstrap.Modal(document.getElementById('categoryModal')); }
        function getBlogModal()    { return blogModal    ||= new bootstrap.Modal(document.getElementById('blogModal')); }
        function getStockModal()   { return stockModal   ||= new bootstrap.Modal(document.getElementById('stockModal')); }

        document.addEventListener("DOMContentLoaded", () => {
            renderCategoriesSelect();
            renderCategoriesTable();
            renderProducts();
            renderBlogsTable();
        });

        function switchView(viewId, el) {
            document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
            el.classList.add('active');

            const titles = {
                'dashboard': 'Tổng quan',
                'products': 'Quản lý Sản phẩm',
                'categories': 'Quản lý Danh mục',
                'blogs': 'Quản lý Tin tức',
                'settings': 'Cấu hình Website',
                'chat': 'Hộp thư hỗ trợ'
            };
            document.getElementById('page-title').innerText = titles[viewId];

            document.querySelectorAll('.view-section').forEach(view => view.classList.remove('active'));
            document.getElementById('view-' + viewId).classList.add('active');
        }

        function renderCategoriesSelect() {
            const select = document.getElementById('p_category');
            select.innerHTML = APP_STATE.categories.map(cat => `<option value="${cat.slug}">${cat.name}</option>`).join('');
        }

        function renderCategoriesTable() {
            const tbody = document.getElementById('category-table-body');
            tbody.innerHTML = '';

            APP_STATE.categories.forEach(cat => {
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold text-dark">${cat.name}</td>
                        <td><code>${cat.slug}</code></td>
                        <td>${cat.is_pro ? '<span class="badge bg-primary rounded-pill">Có (Glow)</span>' : '<span class="text-muted">Không</span>'}</td>
                        <td>${cat.icon ? `<i class="fa-solid ${cat.icon} ${cat.icon_color || ''}"></i>` : '-'}</td>
                        <td class="text-end">
                            <button class="btn-action" onclick="editCategory(${cat.id})" title="Sửa"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="deleteCategory(${cat.id})" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }

        function getCategoryName(id) {
            const cat = APP_STATE.categories.find(c => c.id === id);
            return cat ? cat.name : id;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        function renderProducts() {
            const tbody = document.getElementById('product-table-body');
            tbody.innerHTML = '';

            APP_STATE.products.forEach(p => {
                let badgeClass = p.status === 'active' ? 'bg-success' : (p.status === 'out_of_stock' ? 'bg-warning text-dark' : 'bg-secondary');
                let statusText = p.status === 'active' ? 'Đang bán' : (p.status === 'out_of_stock' ? 'Hết hàng' : 'Đã ẩn');

                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${p.image}" class="img-thumbnail-custom me-3" alt="${p.title}">
                                <div>
                                    <div class="fw-bold text-dark">${p.title}</div>
                                    <div class="text-muted small">${p.feature_text || p.description}</div>
                                </div>
                            </div>
                        </td>
                        <td>${getCategoryName(p.category_slug || p.category)}</td>
                        <td class="fw-bold">${typeof p.price === 'number' ? formatCurrency(p.price) : p.price}</td>
                        <td><span class="badge ${badgeClass} rounded-pill">${statusText}</span></td>
                        <td class="text-end">
                            <button class="btn-action" onclick="editProduct('${p.id}')" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn-action delete" onclick="deleteProduct('${p.id}')" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            document.getElementById('dash-total-products').innerText = APP_STATE.products.length;
        }

        function openProductModal() {
            document.getElementById('productForm').reset();
            document.getElementById('p_id').value = '';
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
                document.getElementById('p_status').value = p.status || 'active';
                document.getElementById('p_image').value = p.image;
                document.getElementById('p_desc').value = p.feature_text || '';
                document.getElementById('p_detail_desc').value = p.description || '';

                renderVariants(p.options || []);

                document.getElementById('modalTitle').innerText = "Chỉnh sửa Sản phẩm";
                getProductModal().show();
            }
        }

        function addVariantRow(data = { name: '', price: '', original_price: '', stock: '', is_upgrade: 0 }) {
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
                <div class="col-md-2 d-flex justify-content-center align-items-center gap-2">
                    <div class="form-check form-switch mb-0" title="Yêu cầu nâng cấp chính chủ">
                        <input class="form-check-input v-upgrade" type="checkbox" ${data.is_upgrade == 1 ? 'checked' : ''}>
                        <label class="form-check-label smaller">Up</label>
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

        function saveProduct() {
            const variants = [];
            document.querySelectorAll('.variant-row').forEach(row => {
                const original = parseFloat(row.querySelector('.v-original-price').value) || 0;
                const price    = parseFloat(row.querySelector('.v-price').value) || 0;
                variants.push({
                    name: row.querySelector('.v-name').value,
                    price: price,
                    original_price: original > price ? original : 0,
                    stock: row.querySelector('.v-stock').value,
                    is_upgrade: row.querySelector('.v-upgrade').checked ? 1 : 0
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
            formData.append('status', document.getElementById('p_status').value);
            formData.append('image', document.getElementById('p_image').value);
            formData.append('desc', document.getElementById('p_desc').value);
            formData.append('description', document.getElementById('p_detail_desc').value);
            formData.append('variants', JSON.stringify(variants));

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

            fetch('?action=adminSaveSettings', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                body: (() => { formData.append('csrf_token', APP_STATE.csrfToken); return formData; })(),
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        AppNotify.success('Cấu hình website đã được cập nhật.', 'Lưu thành công');
                    } else {
                        AppNotify.error(data.message || 'Không thể lưu cấu hình.', 'Lỗi lưu');
                    }
                });
        }

        function saveTelegramSettings() {
            const fd = new FormData();
            // Only send telegram fields (other fields use defaults from DB)
            fd.append('telegram_bot_token', document.getElementById('st_telegram_bot_token').value);
            fd.append('telegram_chat_id', document.getElementById('st_telegram_chat_id').value);
            // Must include all allowed keys - send current values for everything else
            ['bannerText','zalo','footerDesc','socialLink','copyright','terms_of_service','privacy_policy',
             'sepay_active','sepay_mode','sepay_token','sepay_merchant_id','sepay_api_key',
             'bank_id','bank_account','bank_name','about_title','about_desc','about_image',
             'about_stat_value','about_stat_label','about_features','contact_title','contact_desc',
             'contact_methods','social_links_json','demo_payment_active'].forEach(k => {
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
                    AppNotify.success('Cấu hình Telegram đã được lưu.', 'Lưu thành công');
                } else {
                    AppNotify.error(d.message || 'Không thể lưu.', 'Lỗi');
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
        function renderBlogsTable() {
            const tbody = document.getElementById('blog-table-body');
            tbody.innerHTML = '';
            APP_STATE.blogs.forEach(blog => {
                const dateStr = blog.created_at ? new Date(blog.created_at.replace(' ', 'T')).toLocaleDateString('vi-VN') : '';
                const escTitle = (blog.title || '').replace(/"/g, '&quot;');
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${blog.image || ''}" class="img-thumbnail-custom me-3" style="width: 80px; height: 45px;" alt="${escTitle}">
                                <div class="fw-bold text-dark">${escTitle}</div>
                            </div>
                        </td>
                        <td>${dateStr}</td>
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
            document.getElementById('blog_desc_editor').innerHTML = '';
            setBlogImagePreview('');
            document.querySelector('#blogModal .modal-title').innerText = 'Thêm bài viết mới';
            getBlogModal().show();
        }

        function editBlog(id) {
            const blog = APP_STATE.blogs.find(b => b.id == id);
            if (!blog) return;
            document.getElementById('blog_id').value = blog.id;
            document.getElementById('blog_title').value = blog.title || '';
            document.getElementById('blog_image_url').value = blog.image || '';
            document.getElementById('blog_desc_editor').innerHTML = blog.description || '';
            document.getElementById('blog_desc').value = blog.description || '';
            setBlogImagePreview(blog.image || '');
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
        (function setupBlogModal() {
            const fileInput = document.getElementById('blog_image_file');
            const clearBtn  = document.getElementById('blog_image_clear');

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
            const editor = document.getElementById('blog_desc_editor');
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
                });
            });
        })();

        function saveBlog() {
            const editor = document.getElementById('blog_desc_editor');
            document.getElementById('blog_desc').value = editor.innerHTML.trim();

            const formData = new FormData();
            formData.append('id', document.getElementById('blog_id').value);
            formData.append('title', document.getElementById('blog_title').value);
            formData.append('image', document.getElementById('blog_image_url').value);
            formData.append('description', document.getElementById('blog_desc').value);
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

        // ============== ADMIN CHAT ==============
        let activeChatUserId = null;
        let lastAdminMsgId = 0;
        let lastUnreadCount = 0;

        function escHtml(s) {
            return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        function playAdminNotificationSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                // Note 1: D5
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, audioCtx.currentTime);
                gain1.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
                osc1.start(audioCtx.currentTime);
                osc1.stop(audioCtx.currentTime + 0.4);
                
                // Note 2: A5
                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880.00, audioCtx.currentTime + 0.12);
                gain2.gain.setValueAtTime(0.15, audioCtx.currentTime + 0.12);
                gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.62);
                osc2.start(audioCtx.currentTime + 0.12);
                osc2.stop(audioCtx.currentTime + 0.62);
            } catch (e) {
                console.error("Audio error:", e);
            }
        }

        function refreshChatBadge() {
            fetch('?action=adminChatUnread', { credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        const badge = document.getElementById('admin-chat-badge');
                        const n = d.unread || 0;
                        lastUnreadCount = n;
                        if (n > 0) { badge.textContent = n > 99 ? '99+' : n; badge.classList.remove('d-none'); }
                        else { badge.classList.add('d-none'); }
                    }
                })
                .catch(()=>{});
        }

        function loadChatThreads() {
            fetch('?action=adminChatThreads', { credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => {
                    const list = document.getElementById('chat-threads');
                    list.innerHTML = '';
                    if (!d.threads || d.threads.length === 0) {
                        list.innerHTML = '<div class="text-center text-muted py-5"><i class="fa-regular fa-comments fs-1 opacity-25 mb-3 d-block"></i><p class="mb-0">Chưa có cuộc trò chuyện nào.</p></div>';
                        return;
                    }
                    d.threads.forEach(t => {
                        const initial = (t.name || '?').charAt(0).toUpperCase();
                        const unreadBadge = t.unread > 0
                            ? `<span class="badge bg-danger rounded-pill ms-2">${t.unread}</span>`
                            : '';
                        const activeCls = (parseInt(t.user_id, 10) === parseInt(activeChatUserId, 10)) ? 'active' : '';
                        list.insertAdjacentHTML('beforeend', `
                            <div class="chat-thread-item p-3 border-bottom ${activeCls}" data-user="${t.user_id}" style="cursor:pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:42px;height:42px;">${escHtml(initial)}</div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="fw-bold text-truncate">${escHtml(t.name || '')}</div>
                                            ${unreadBadge}
                                        </div>
                                        <div class="small text-muted text-truncate">${escHtml(t.last_body || '')}</div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                    list.querySelectorAll('.chat-thread-item').forEach(el => {
                        el.addEventListener('click', () => openChatThread(parseInt(el.dataset.user, 10)));
                    });
                    refreshChatBadge();
                });
        }

        function openChatThread(userId) {
            activeChatUserId = userId;
            lastAdminMsgId = 0;
            fetch('?action=adminChatThread&user_id=' + userId, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => {
                    document.getElementById('chat-active-name').textContent = (d.user && d.user.name) || 'Khách';
                    document.getElementById('chat-active-email').textContent = (d.user && d.user.email) || '';
                    const wrap = document.getElementById('chat-messages');
                    wrap.innerHTML = '';
                    (d.messages || []).forEach(m => {
                        const isAdmin = m.sender === 'admin';
                        const time = m.created_at ? new Date(m.created_at.replace(' ', 'T')).toLocaleTimeString('vi-VN', { hour:'2-digit', minute:'2-digit' }) : '';
                        const cls = isAdmin ? 'msg-me' : 'msg-them';
                        const bodyHtml = m.body ? `<div class="bubble">${escHtml(m.body)}</div>` : '';
                        const attHtml  = renderAdminAttachment(m);
                        wrap.insertAdjacentHTML('beforeend',
                            `<div class="chat-msg ${cls}">${bodyHtml}${attHtml ? `<div class="bubble bubble-att">${attHtml}</div>` : ''}<div class="meta">${time}</div></div>`);
                        if (m.id > lastAdminMsgId) {
                            lastAdminMsgId = m.id;
                        }
                    });
                    wrap.scrollTop = wrap.scrollHeight;
                    document.getElementById('chat-reply-form').style.display = 'flex';
                    
                    document.querySelectorAll('.chat-thread-item').forEach(el => {
                        if (parseInt(el.dataset.user, 10) === userId) {
                            el.classList.add('active');
                        } else {
                            el.classList.remove('active');
                        }
                    });
                    
                    refreshChatBadge();
                });
        }

        function renderAdminAttachment(m) {
            if (!m.attachment_path) return '';
            const url = '?action=chatFile&id=' + m.id;
            const isImage = (m.attachment_mime || '').indexOf('image/') === 0;
            if (isImage) {
                return `<a href="${url}" target="_blank" class="chat-att-img"><img src="${url}" alt="${escHtml(m.attachment_name||'image')}"></a>`;
            }
            const sizeKb = m.attachment_size ? (m.attachment_size > 1024*1024
                ? (m.attachment_size/1024/1024).toFixed(2) + ' MB'
                : (m.attachment_size/1024).toFixed(1) + ' KB') : '';
            return `<a href="${url}" target="_blank" class="chat-att-file">
                        <i class="fa-solid fa-file-arrow-down"></i>
                        <div class="chat-att-meta">
                            <div class="fw-bold small text-truncate" style="max-width:240px;">${escHtml(m.attachment_name||'file')}</div>
                            <div class="small text-muted">${sizeKb}</div>
                        </div>
                    </a>`;
        }

        let adminPendingFile = null;
        let adminFileInput, adminAttachBtn, adminPreview, adminReplyInput;

        function clearAdminAttachment() {
            adminPendingFile = null;
            if (adminFileInput) adminFileInput.value = '';
            if (adminPreview) {
                adminPreview.classList.add('d-none');
                adminPreview.innerHTML = '';
            }
        }

        function autoGrowAdmin() {
            if (!adminReplyInput) return;
            adminReplyInput.style.height = 'auto';
            adminReplyInput.style.height = Math.min(adminReplyInput.scrollHeight, 140) + 'px';
        }

        function pollAdminChat() {
            // Global unread check
            fetch('?action=adminChatUnread', { credentials: 'same-origin' })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        const badge = document.getElementById('admin-chat-badge');
                        const n = d.unread || 0;
                        if (n > lastUnreadCount) {
                            playAdminNotificationSound();
                        }
                        lastUnreadCount = n;
                        if (n > 0) { badge.textContent = n > 99 ? '99+' : n; badge.classList.remove('d-none'); }
                        else { badge.classList.add('d-none'); }
                    }
                })
                .catch(()=>{});

            // If we are looking at the chat page
            const chatView = document.getElementById('view-chat');
            if (chatView && chatView.classList.contains('active')) {
                loadChatThreads();
                if (activeChatUserId) {
                    fetch('?action=adminChatThread&user_id=' + activeChatUserId + '&since=' + lastAdminMsgId, { credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(d => {
                            if (!d.messages || d.messages.length === 0) return;
                            const wrap = document.getElementById('chat-messages');
                            let hasNewUserMsg = false;
                            d.messages.forEach(m => {
                                const isAdmin = m.sender === 'admin';
                                const time = m.created_at ? new Date(m.created_at.replace(' ', 'T')).toLocaleTimeString('vi-VN', { hour:'2-digit', minute:'2-digit' }) : '';
                                const cls = isAdmin ? 'msg-me' : 'msg-them';
                                const bodyHtml = m.body ? `<div class="bubble">${escHtml(m.body)}</div>` : '';
                                const attHtml  = renderAdminAttachment(m);
                                wrap.insertAdjacentHTML('beforeend',
                                    `<div class="chat-msg ${cls}">${bodyHtml}${attHtml ? `<div class="bubble bubble-att">${attHtml}</div>` : ''}<div class="meta">${time}</div></div>`);
                                if (m.id > lastAdminMsgId) {
                                    lastAdminMsgId = m.id;
                                }
                                if (m.sender === 'user') {
                                    hasNewUserMsg = true;
                                }
                            });
                            wrap.scrollTop = wrap.scrollHeight;
                            if (hasNewUserMsg) {
                                playAdminNotificationSound();
                            }
                        })
                        .catch(()=>{});
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            adminFileInput = document.getElementById('admin-chat-file');
            adminAttachBtn = document.getElementById('btnAdminAttach');
            adminPreview   = document.getElementById('chat-reply-preview');
            adminReplyInput = document.getElementById('chat-reply-input');
            const replyForm = document.getElementById('chat-reply-form');
            const adminCounter = document.getElementById('admin-chat-counter');

            if (adminAttachBtn) {
                adminAttachBtn.addEventListener('click', () => adminFileInput && adminFileInput.click());
            }

            if (adminFileInput) {
                adminFileInput.addEventListener('change', () => {
                    const f = adminFileInput.files[0];
                    if (!f) return;
                    if (f.size > 10 * 1024 * 1024) {
                        AppNotify.warning('Tối đa 10MB mỗi tệp.', 'Tệp quá lớn');
                        adminFileInput.value = '';
                        return;
                    }
                    adminPendingFile = f;
                    const isImage = f.type.indexOf('image/') === 0;
                    adminPreview.classList.remove('d-none');
                    const sizeText = f.size > 1024*1024 ? (f.size/1024/1024).toFixed(2) + ' MB' : (f.size/1024).toFixed(1) + ' KB';
                    adminPreview.innerHTML = `<div class="d-flex align-items-center gap-2 small">
                        ${isImage ? `<img src="${URL.createObjectURL(f)}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">` : '<i class="fa-solid fa-file fs-4 text-muted"></i>'}
                        <div class="flex-grow-1 text-truncate">${escHtml(f.name)} <span class="text-muted">(${sizeText})</span></div>
                        <button type="button" class="btn btn-sm text-danger p-0 border-0" id="btnAdminRemoveAttach"><i class="fa-solid fa-xmark"></i></button>
                    </div>`;
                    adminPreview.querySelector('#btnAdminRemoveAttach').addEventListener('click', clearAdminAttachment);
                });
            }

            if (adminReplyInput) {
                adminReplyInput.addEventListener('input', () => {
                    autoGrowAdmin();
                    const len = adminReplyInput.value.length;
                    if (len > 0) {
                        adminCounter.textContent = len + '/700';
                        adminCounter.style.display = 'block';
                        if (len >= 700) {
                            adminCounter.classList.add('text-danger');
                        } else {
                            adminCounter.classList.remove('text-danger');
                        }
                    } else {
                        adminCounter.style.display = 'none';
                    }
                });
                adminReplyInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        if (replyForm) replyForm.requestSubmit();
                    }
                });
            }

            if (replyForm) {
                replyForm.addEventListener('submit', e => {
                    e.preventDefault();
                    if (!activeChatUserId) return;
                    const body = adminReplyInput.value.trim();
                    if (!body && !adminPendingFile) return;
                    const fd = new FormData();
                    fd.append('user_id', activeChatUserId);
                    fd.append('body', body);
                    fd.append('csrf_token', APP_STATE.csrfToken);
                    if (adminPendingFile) fd.append('file', adminPendingFile);
                    fetch('?action=adminChatSend', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': APP_STATE.csrfToken },
                        body: fd, credentials: 'same-origin'
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            adminReplyInput.value = '';
                            autoGrowAdmin();
                            clearAdminAttachment();
                            if (adminCounter) adminCounter.style.display = 'none';
                            pollAdminChat();
                        } else {
                            AppNotify.error(d.message || 'Không thể gửi.', 'Lỗi gửi');
                        }
                    });
                });
            }
        });

        // Poll chat every 4s
        pollAdminChat();
        setInterval(pollAdminChat, 4000);
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
    </script>

    <!-- Modals for Blog and Orders -->
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
                            <div id="blog_desc_editor" class="rich-editor" contenteditable="true"></div>
                            <textarea id="blog_desc" class="d-none"></textarea>
                            <small class="text-muted d-block mt-1">Mẹo: bôi đen text rồi chọn nút trên thanh công cụ để định dạng.</small>
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
                                <input type="text" class="form-control" id="cat_icon" placeholder="fa-sparkles">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Màu Icon (Class)</label>
                                <input type="text" class="form-control" id="cat_icon_color" placeholder="text-primary">
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
</body>

</html>
