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
                    <a href="#" class="nav-link" onclick="switchView('settings', this)">
                        <i class="fa-solid fa-gear"></i> Cấu hình Website
                    </a>
                </li>
            </ul>
            <div class="p-3 border-top" style="border-color: #333 !important;">
                <div class="d-flex align-items-center text-white">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=fff&color=000"
                        class="rounded-circle me-2" width="35">
                    <div style="font-size: 0.85rem;">
                        <div class="fw-bold">Admin</div>
                        <div class="text-secondary">admin@aicualtoi.com</div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h5 class="mb-0 fw-bold" id="page-title">Quản lý Sản phẩm</h5>
                <div>
                    <button class="btn btn-light border-0 shadow-sm me-2"><i class="fa-regular fa-bell"></i></button>
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

                <div id="view-settings" class="view-section">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-custom p-4 mb-4">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">Header & Mini Banner</h5>
                                <form id="headerSettingsForm">
                                    <div class="mb-3">
                                        <label class="form-label">Dòng chữ Mini Banner</label>
                                        <input type="text" class="form-control" id="st_bannerText" value="<?php echo htmlspecialchars($settings['bannerText']); ?>">
                                        <small class="text-muted">Hiển thị dòng chữ nhỏ chạy trên cùng web.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại Zalo/Hotline</label>
                                        <input type="text" class="form-control" id="st_zalo" value="<?php echo htmlspecialchars($settings['zalo']); ?>">
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
                                        <textarea class="form-control" id="st_footerDesc" rows="3"><?php echo htmlspecialchars($settings['footerDesc']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link Facebook/Twitter (Tùy chọn)</label>
                                        <input type="text" class="form-control" id="st_socialLink"
                                            placeholder="https://..." value="<?php echo htmlspecialchars($settings['socialLink']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tên bản quyền (Copyright)</label>
                                        <input type="text" class="form-control" id="st_copyright" value="<?php echo htmlspecialchars($settings['copyright']); ?>">
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
                </div>

                <div id="view-dashboard" class="view-section">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="card-custom p-4">
                                <div class="text-muted fw-bold mb-2">DOANH THU THÁNG</div>
                                <h3 class="fw-bold text-dark">12.500.000đ</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom p-4">
                                <div class="text-muted fw-bold mb-2">ĐƠN HÀNG MỚI</div>
                                <h3 class="fw-bold text-dark">48</h3>
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
                                <h3 class="fw-bold text-dark" id="dash-total-products"><?php echo count($products); ?></h3>
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

    <script>
        const APP_STATE = {
            categories: [
                { id: 'chatgpt', name: 'ChatGPT' },
                { id: 'youtube', name: 'YouTube' },
                { id: 'netflix', name: 'Netflix' },
                { id: 'github', name: 'Github' }
            ],
            settings: <?php echo json_encode($settings); ?>,
            products: <?php echo json_encode($products); ?>
        };

        const productModal = new bootstrap.Modal(document.getElementById('productModal'));

        document.addEventListener("DOMContentLoaded", () => {
            renderCategoriesSelect();
            renderProducts();
            // loadSettings is already done via value="<?php ... ?>"
        });

        function switchView(viewId, el) {
            document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
            el.classList.add('active');

            const titles = {
                'dashboard': 'Tổng quan',
                'products': 'Quản lý Sản phẩm',
                'settings': 'Cấu hình Website'
            };
            document.getElementById('page-title').innerText = titles[viewId];

            document.querySelectorAll('.view-section').forEach(view => view.classList.remove('active'));
            document.getElementById('view-' + viewId).classList.add('active');
        }

        function renderCategoriesSelect() {
            const select = document.getElementById('p_category');
            select.innerHTML = APP_STATE.categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
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
                                    <div class="text-muted small">${p.desc}</div>
                                </div>
                            </div>
                        </td>
                        <td>${getCategoryName(p.category)}</td>
                        <td class="fw-bold">${formatCurrency(p.price)}</td>
                        <td><span class="badge ${badgeClass} rounded-pill">${statusText}</span></td>
                        <td class="text-end">
                            <button class="btn-action" onclick="editProduct(${p.id})" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn-action delete" onclick="deleteProduct(${p.id})" title="Xóa"><i class="fa-solid fa-trash"></i></button>
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
            productModal.show();
        }

        function editProduct(id) {
            const p = APP_STATE.products.find(item => item.id == id);
            if (p) {
                document.getElementById('p_id').value = p.id;
                document.getElementById('p_title').value = p.title;
                document.getElementById('p_category').value = p.category;
                document.getElementById('p_price').value = p.price;
                document.getElementById('p_status').value = p.status;
                document.getElementById('p_image').value = p.image;
                document.getElementById('p_desc').value = p.desc;

                document.getElementById('modalTitle').innerText = "Chỉnh sửa Sản phẩm";
                productModal.show();
            }
        }

        function saveProduct() {
            // Implementation for saving product via AJAX can be added here
            toastMsg('Tính năng này đang được phát triển với Database!');
            productModal.hide();
        }

        function deleteProduct(id) {
            Swal.fire({
                title: 'Xóa sản phẩm này?',
                text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#111',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    toastMsg('Đã xóa sản phẩm (giả lập)!');
                }
            })
        }

        function saveSettings() {
            const formData = new FormData();
            formData.append('bannerText', document.getElementById('st_bannerText').value);
            formData.append('zalo', document.getElementById('st_zalo').value);
            formData.append('footerDesc', document.getElementById('st_footerDesc').value);
            formData.append('socialLink', document.getElementById('st_socialLink').value);
            formData.append('copyright', document.getElementById('st_copyright').value);

            fetch('?action=adminSaveSettings', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Lưu thành công',
                        text: 'Cấu hình website đã được cập nhật.',
                        confirmButtonColor: '#000',
                    });
                }
            });
        }

        function toastMsg(msg) {
            Swal.fire({
                toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000,
                background: '#111', color: '#fff', icon: 'success', title: msg, iconColor: '#fff',
                customClass: { popup: 'rounded-4' }
            });
        }
    </script>
</body>

</html>
