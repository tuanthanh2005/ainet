<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Toàn Hệ Thống - Admin Panel</title>

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
            --success-color: #10b981;
            --error-color: #ef4444;
            --pending-color: #6b7280;
            --warning-color: #f59e0b;
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

        .nav-link:hover, .nav-link.active {
            color: var(--pure-white);
            background-color: #1f1f1f;
        }

        .nav-link i {
            margin-right: 12px;
            width: 18px;
            text-align: center;
        }

        .nav-label {
            flex: 1;
            min-width: 0;
        }

        .nav-count-badge {
            min-width: 24px;
            height: 24px;
            padding: 0 7px;
            border-radius: 999px;
            background: #f59e0b;
            color: #111;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
            line-height: 1;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.08);
        }

        /* MAIN CONTENT */
        .main-content {
            flex-grow: 1;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background-color: var(--pure-white);
            border-bottom: 1px solid var(--border-color);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-area {
            padding: 30px;
            flex-grow: 1;
        }

        /* ================= COMPONENTS ================= */
        .card-custom {
            background-color: var(--pure-white);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header-custom {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--pure-white);
        }

        .btn-black {
            background-color: var(--pure-black);
            color: var(--pure-white);
            border: 1px solid var(--pure-black);
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-black:hover:not(:disabled) {
            background-color: #222;
            color: var(--pure-white);
            border-color: #222;
        }

        .btn-black:disabled {
            background-color: #cccccc;
            border-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
        }

        /* Table custom styling */
        .table-custom th {
            background-color: #fafafa;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--mid-gray);
            border-bottom: 1px solid var(--border-color);
            padding: 14px 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom td {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .table-custom tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Progress Bar Animation */
        .progress {
            height: 12px;
            border-radius: 6px;
            background-color: #e5e7eb;
            overflow: hidden;
        }

        .progress-bar {
            background-color: var(--pure-black);
            transition: width 0.3s ease;
        }

        /* Log console styling */
        .log-console {
            background-color: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.82rem;
            padding: 15px;
            border-radius: 8px;
            height: 250px;
            overflow-y: auto;
            white-space: pre-wrap;
            border: 1px solid #333;
        }

        .badge-pending { background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
        .badge-running { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-success { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-failed { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        .spinner-mini {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .filter-active {
            background-color: var(--pure-black) !important;
            color: var(--pure-white) !important;
            border-color: var(--pure-black) !important;
        }

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
            .btn-black, .btn-light {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
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
                    <a href="index.php?action=adminDashboard&tab=dashboard" class="nav-link">
                        <i class="fa-solid fa-chart-pie"></i> Tổng quan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=adminDashboard&tab=products" class="nav-link">
                        <i class="fa-solid fa-box"></i> Quản lý Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=adminDashboard&tab=orders" class="nav-link">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="nav-label">Quản lý Đơn hàng</span>
                        <?php if (!empty($pendingOrders)): ?>
                            <span class="nav-count-badge" title="Đơn cần xử lý"><?php echo (int) $pendingOrders; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=adminDashboard&tab=contacts" class="nav-link">
                        <i class="fa-solid fa-envelope"></i>
                        <span class="nav-label">Quản lý Liên hệ</span>
                        <?php if (!empty($unreadContacts)): ?>
                            <span class="nav-count-badge" id="contact-unread-badge" title="Tin liên hệ mới"><?php echo (int) $unreadContacts; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=adminDashboard&tab=users" class="nav-link">
                        <i class="fa-solid fa-users"></i> Quản lý User
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=adminDashboard&tab=categories" class="nav-link">
                        <i class="fa-solid fa-list-ul"></i> Quản lý Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=adminDashboard&tab=blogs" class="nav-link">
                        <i class="fa-solid fa-newspaper"></i> Quản lý Tin tức
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=adminDashboard&tab=settings" class="nav-link">
                        <i class="fa-solid fa-gear"></i> Cấu hình Website
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Index Toàn Hệ Thống
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=adminDashboard&tab=keywords" class="nav-link">
                        <i class="fa-solid fa-key"></i> Quản lý Từ khóa SEO
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
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light border shadow-sm me-1 d-lg-none" onclick="toggleSidebar()" aria-label="Toggle Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h5 class="mb-0 fw-bold" id="page-title">Index Toàn Hệ Thống</h5>
                </div>
                <div class="d-flex align-items-center gap-1 gap-md-2">
                    <button class="btn btn-light border-0 shadow-sm" title="Thông báo"><i class="fa-regular fa-bell"></i></button>
                    <a href="index.php?action=logout" class="btn btn-light border shadow-sm" title="Đăng xuất">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="d-none d-sm-inline ms-1 ms-md-2">Đăng xuất</span>
                    </a>
                    <a href="index.php" target="_blank" class="btn btn-black" title="Xem Website">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span class="d-none d-sm-inline ms-1 ms-md-2">Xem Website</span>
                    </a>
                </div>
            </header>

            <div class="content-area">
                <!-- Check Configuration Alert -->
                <?php if (!$google_indexing_enabled): ?>
                    <div class="alert alert-warning border-warning d-flex align-items-center" role="alert">
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
                        <div>
                            <strong>Cảnh báo:</strong> Google Indexing API hiện tại đang bị <strong>Tắt (False)</strong> trong cấu hình hệ thống (tệp <code>.env</code>). Hãy bật <code>GOOGLE_INDEXING_ENABLED=true</code> để việc gửi yêu cầu chỉ mục hoạt động.
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($google_indexing_credentials)): ?>
                    <div class="alert alert-danger border-danger d-flex align-items-center" role="alert">
                        <i class="fa-solid fa-circle-exclamation fs-4 me-3"></i>
                        <div>
                            <strong>Lỗi cấu hình:</strong> Chưa tìm thấy tệp tài khoản dịch vụ Google Indexing (<code>GOOGLE_INDEXING_CREDENTIALS</code>). Bạn cần cấu hình đường dẫn tệp JSON tài khoản dịch vụ Google để có thể index.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Status Panel -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card-custom p-4 text-center border-top border-dark" style="border-width: 4px !important;">
                            <small class="text-muted text-uppercase fw-bold">Tổng số URL</small>
                            <h2 class="fw-bold mt-2 mb-0" id="stat-total">0</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-custom p-4 text-center border-top border-warning" style="border-width: 4px !important;">
                            <small class="text-muted text-uppercase fw-bold">Chờ xử lý</small>
                            <h2 class="fw-bold mt-2 mb-0 text-warning" id="stat-pending">0</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-custom p-4 text-center border-top border-success" style="border-width: 4px !important;">
                            <small class="text-muted text-uppercase fw-bold">Thành công</small>
                            <h2 class="fw-bold mt-2 mb-0 text-success" id="stat-success">0</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-custom p-4 text-center border-top border-danger" style="border-width: 4px !important;">
                            <small class="text-muted text-uppercase fw-bold">Thất bại</small>
                            <h2 class="fw-bold mt-2 mb-0 text-danger" id="stat-failed">0</h2>
                        </div>
                    </div>
                </div>

                <!-- Control Panel Card -->
                <div class="card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold mb-1">Tiến trình Indexing</h5>
                            <p class="text-muted small mb-0" id="progress-text">Chưa bắt đầu gửi yêu cầu.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-black" id="btn-start" onclick="startIndexing()" <?php echo (!$google_indexing_enabled || empty($google_indexing_credentials)) ? 'disabled' : ''; ?>>
                                <i class="fa-solid fa-play me-2"></i>Bắt đầu Index toàn hệ thống
                            </button>
                            <button class="btn btn-outline-danger" id="btn-stop" onclick="stopIndexing()" disabled>
                                <i class="fa-solid fa-stop me-2"></i>Dừng lại
                            </button>
                        </div>
                    </div>
                    <div class="progress mt-4">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- URL List Card -->
                <div class="card-custom">
                    <div class="card-header-custom flex-column align-items-stretch gap-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Danh sách chi tiết URL trong hệ thống</h6>
                                <small class="text-muted">Quản lý và theo dõi kết quả gửi chỉ mục của từng trang con</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" class="form-control form-control-sm" id="search-input" placeholder="Tìm kiếm URL..." style="width: 250px;" oninput="filterUrls()">
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap border-top pt-3">
                            <button class="btn btn-light btn-sm border filter-btn filter-active" data-filter="all" onclick="setFilter('all', this)">Tất cả</button>
                            <button class="btn btn-light btn-sm border filter-btn" data-filter="pending" onclick="setFilter('pending', this)">Chờ xử lý</button>
                            <button class="btn btn-light btn-sm border filter-btn" data-filter="success" onclick="setFilter('success', this)">Thành công</button>
                            <button class="btn btn-light btn-sm border filter-btn" data-filter="failed" onclick="setFilter('failed', this)">Thất bại</button>
                            
                            <div class="ms-auto d-flex gap-2">
                                <button class="btn btn-light btn-sm border" onclick="loadUrlsList()">
                                    <i class="fa-solid fa-rotate me-1"></i> Tải lại danh sách
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover table-custom mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th>Đường dẫn (URL)</th>
                                    <th style="width: 150px;">Loại trang</th>
                                    <th style="width: 150px;">Trạng thái</th>
                                    <th>Thông điệp phản hồi / Chi tiết lỗi</th>
                                </tr>
                            </thead>
                            <tbody id="url-table-body">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <span class="spinner-border spinner-border-sm me-2"></span> Đang tải danh sách URL của hệ thống...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Logs console -->
                <div class="card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="fa-solid fa-terminal me-2"></i>Nhật ký Indexing gần đây (indexing.log)</h6>
                        <button class="btn btn-light btn-sm border" onclick="loadLogs()">
                            <i class="fa-solid fa-arrows-rotate"></i> Làm mới Log
                        </button>
                    </div>
                    <div class="log-console" id="log-viewer">Chưa có thông tin log.</div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let allUrls = [];
        let isRunning = false;
        let currentIndex = 0;
        let successCount = 0;
        let failedCount = 0;
        let currentFilter = 'all';

        document.addEventListener("DOMContentLoaded", () => {
            loadUrlsList();
            loadLogs();
        });

        // 1. Load URLs from server
        function loadUrlsList() {
            if (isRunning) return;

            const tbody = document.getElementById('url-table-body');
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm me-2"></span>Đang tải danh sách URL...</td></tr>`;

            fetch('?action=adminGetAllUrls', { credentials: 'same-origin' })
                .then(res => res.json())
                .then(data => {
                    if (data.success && Array.isArray(data.urls)) {
                        allUrls = data.urls.map(item => ({
                            url: item.url,
                            type: item.type,
                            status: 'pending', // pending, running, success, failed
                            message: 'Đang chờ xử lý...'
                        }));
                        updateStats();
                        renderTable();
                    } else {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Lỗi: ${data.message || 'Không thể tải danh sách URL.'}</td></tr>`;
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Lỗi kết nối: ${err.message}</td></tr>`;
                });
        }

        // 2. Load indexing.log from server
        function loadLogs() {
            const viewer = document.getElementById('log-viewer');
            viewer.innerText = 'Đang đọc log...';
            fetch('?action=adminGetIndexingLogs', { credentials: 'same-origin' })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        viewer.innerText = data.logs || 'Chưa có bản ghi log nào.';
                        viewer.scrollTop = viewer.scrollHeight; // Scroll to bottom
                    } else {
                        viewer.innerText = 'Lỗi tải log: ' + data.message;
                    }
                })
                .catch(err => {
                    viewer.innerText = 'Lỗi kết nối: ' + err.message;
                });
        }

        // 3. Render list table
        function renderTable() {
            const tbody = document.getElementById('url-table-body');
            const searchVal = document.getElementById('search-input').value.toLowerCase().trim();

            let filtered = allUrls.filter(item => {
                // Filter by status
                if (currentFilter !== 'all' && item.status !== currentFilter) {
                    return false;
                }
                // Filter by search query
                if (searchVal && !item.url.toLowerCase().includes(searchVal)) {
                    return false;
                }
                return true;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Không tìm thấy URL nào khớp với điều kiện.</td></tr>`;
                return;
            }

            tbody.innerHTML = '';
            filtered.forEach((item, index) => {
                let badgeClass = 'badge-pending';
                let badgeText = 'Chờ xử lý';
                if (item.status === 'running') {
                    badgeClass = 'badge-running';
                    badgeText = '<span class="spinner-mini me-1"></span> Đang gửi';
                } else if (item.status === 'success') {
                    badgeClass = 'badge-success';
                    badgeText = '<i class="fa-solid fa-circle-check me-1"></i> Thành công';
                } else if (item.status === 'failed') {
                    badgeClass = 'badge-failed';
                    badgeText = '<i class="fa-solid fa-circle-xmark me-1"></i> Thất bại';
                }

                let messageColor = '';
                if (item.status === 'success') messageColor = 'text-success';
                if (item.status === 'failed') messageColor = 'text-danger fw-medium';

                tbody.innerHTML += `
                    <tr id="url-row-${index}">
                        <td>${index + 1}</td>
                        <td>
                            <a href="${item.url}" target="_blank" class="text-decoration-none text-dark fw-medium text-break">
                                ${item.url} <i class="fa-solid fa-arrow-up-right-from-square ms-1 text-muted" style="font-size: 0.75rem;"></i>
                            </a>
                        </td>
                        <td><span class="badge bg-light text-dark border px-2 py-1">${item.type}</span></td>
                        <td><span class="badge ${badgeClass} px-2 py-1.5">${badgeText}</span></td>
                        <td class="${messageColor}">${item.message}</td>
                    </tr>
                `;
            });
        }

        // 4. Set filter tab
        function setFilter(filterType, btn) {
            currentFilter = filterType;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('filter-active'));
            btn.classList.add('filter-active');
            renderTable();
        }

        // 5. Filter search
        function filterUrls() {
            renderTable();
        }

        // 6. Update statistics widgets
        function updateStats() {
            const pendingCount = allUrls.filter(u => u.status === 'pending' || u.status === 'running').length;
            
            document.getElementById('stat-total').innerText = allUrls.length;
            document.getElementById('stat-pending').innerText = pendingCount;
            document.getElementById('stat-success').innerText = successCount;
            document.getElementById('stat-failed').innerText = failedCount;

            const percentage = allUrls.length > 0 ? Math.round(((successCount + failedCount) / allUrls.length) * 100) : 0;
            const pb = document.getElementById('progress-bar');
            pb.style.width = percentage + '%';
            pb.setAttribute('aria-valuenow', percentage);

            const statTotalProcessed = successCount + failedCount;
            if (isRunning) {
                document.getElementById('progress-text').innerHTML = `Đang xử lý: <strong>${statTotalProcessed}/${allUrls.length}</strong> URL (${percentage}%). Đã xong: ${successCount} thành công, ${failedCount} thất bại.`;
            } else {
                if (statTotalProcessed === 0) {
                    document.getElementById('progress-text').innerText = 'Chưa bắt đầu gửi yêu cầu.';
                } else if (statTotalProcessed === allUrls.length) {
                    document.getElementById('progress-text').innerHTML = `<span class="text-success fw-bold"><i class="fa-solid fa-circle-check"></i> Hoàn thành!</span> Đã xử lý xong <strong>${allUrls.length}/${allUrls.length}</strong> URL. ${successCount} thành công, ${failedCount} thất bại.`;
                } else {
                    document.getElementById('progress-text').innerHTML = `<span class="text-warning fw-bold"><i class="fa-solid fa-circle-pause"></i> Đã dừng.</span> Đã xử lý <strong>${statTotalProcessed}/${allUrls.length}</strong> URL.`;
                }
            }
        }

        // 7. Start Indexing Loop
        function startIndexing() {
            if (allUrls.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Không có URL', text: 'Chưa tải được danh sách URL nào trong hệ thống.' });
                return;
            }

            // Check if there are pending items, if not reset
            const pendingItems = allUrls.filter(u => u.status === 'pending');
            if (pendingItems.length === 0) {
                // Ask if they want to re-run
                Swal.fire({
                    title: 'Chạy lại toàn bộ?',
                    text: 'Tất cả các URL đã được xử lý trước đó. Bạn có muốn reset trạng thái và chạy lại từ đầu không?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#000000',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        resetAndStartIndexing();
                    }
                });
                return;
            }

            isRunning = true;
            currentIndex = allUrls.findIndex(u => u.status === 'pending');
            
            document.getElementById('btn-start').disabled = true;
            document.getElementById('btn-stop').disabled = false;
            
            processNextUrl();
        }

        function resetAndStartIndexing() {
            allUrls.forEach(u => {
                u.status = 'pending';
                u.message = 'Đang chờ xử lý...';
            });
            successCount = 0;
            failedCount = 0;
            isRunning = true;
            currentIndex = 0;

            document.getElementById('btn-start').disabled = true;
            document.getElementById('btn-stop').disabled = false;
            
            updateStats();
            renderTable();
            processNextUrl();
        }

        function stopIndexing() {
            isRunning = false;
            document.getElementById('btn-start').disabled = false;
            document.getElementById('btn-stop').disabled = true;
            
            // Mark running item back to pending
            allUrls.forEach(u => {
                if (u.status === 'running') {
                    u.status = 'pending';
                    u.message = 'Chờ xử lý (bị dừng)...';
                }
            });

            updateStats();
            renderTable();
            loadLogs();
            
            Swal.fire({
                title: 'Đã dừng indexing',
                text: 'Quá trình index hàng loạt đã được dừng lại theo yêu cầu.',
                icon: 'info',
                confirmButtonColor: '#000000'
            });
        }

        // 8. Recursive execution function (Sequentially process to avoid server overloading/quota spikes)
        function processNextUrl() {
            if (!isRunning) return;

            if (currentIndex >= allUrls.length) {
                // Completed!
                isRunning = false;
                document.getElementById('btn-start').disabled = false;
                document.getElementById('btn-stop').disabled = true;
                updateStats();
                loadLogs();
                Swal.fire({
                    title: 'Hoàn thành!',
                    html: `Đã gửi index xong toàn bộ hệ thống.<br><b class="text-success">${successCount} thành công</b>, <b class="text-danger">${failedCount} thất bại</b>.`,
                    icon: 'success',
                    confirmButtonColor: '#000000'
                });
                return;
            }

            const item = allUrls[currentIndex];
            item.status = 'running';
            item.message = 'Đang gửi yêu cầu...';
            
            updateStats();
            renderTable();

            const fd = new FormData();
            fd.append('url', item.url);
            fd.append('csrf_token', '<?php echo Csrf::token(); ?>');

            fetch('?action=adminPushIndexSingleUrl', {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'X-CSRF-Token': '<?php echo Csrf::token(); ?>' 
                },
                body: fd,
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
                if (!isRunning) return; // ignore response if stopped

                if (data.success) {
                    item.status = 'success';
                    item.message = 'Đã gửi thành công: Google ' + (data.message || 'Submitted');
                    successCount++;
                } else {
                    item.status = 'failed';
                    // Extract message from response if available
                    let errDetail = data.message || 'Lỗi không xác định.';
                    if (data.response && typeof data.response === 'object') {
                        if (data.response.body && typeof data.response.body === 'object' && data.response.body.error) {
                            errDetail += ' (' + (data.response.body.error.message || JSON.stringify(data.response.body.error)) + ')';
                        } else {
                            errDetail += ' (' + JSON.stringify(data.response) + ')';
                        }
                    }
                    item.message = errDetail;
                    failedCount++;
                }

                // Advance
                currentIndex++;
                
                // Periodically refresh logs every 5 items to show output
                if (currentIndex % 5 === 0) {
                    loadLogs();
                }

                // Call next URL
                processNextUrl();
            })
            .catch(err => {
                if (!isRunning) return;
                item.status = 'failed';
                item.message = 'Lỗi kết nối mạng: ' + err.message;
                failedCount++;
                currentIndex++;
                processNextUrl();
            });
        }

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
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
                // Force reflow
                backdrop.offsetHeight;
                sidebar.classList.add('show');
                backdrop.classList.add('show');
            }
        }
    </script>
</body>

</html>
