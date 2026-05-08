<div id="products-section">
    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom border-light">
        <div class="tab-nav">
            <button class="tab-btn active" onclick="switchTab('products', event)">Sản Phẩm</button>
            <button class="tab-btn" onclick="switchTab('blog', event)">Tạp Chí (Blog)</button>
        </div>

        <select class="form-select w-auto border-0 bg-transparent fw-semibold shadow-none cursor-pointer">
            <option>Mới cập nhật</option>
            <option>Giá: Thấp đến Cao</option>
            <option>Bán chạy nhất</option>
        </select>
    </div>

    <!-- Category Pill Menu -->
    <div class="category-menu-wrapper fade-in-element" style="animation-delay: 0.1s;">
        <button class="cat-pill active" onclick="filterProducts('all', event)">Tất Cả</button>
        <button class="cat-pill pro-glow" onclick="filterProducts('chatgpt', event)"><i
                class="fa-solid fa-sparkles text-primary"></i> Antigravity PRO</button>
        <button class="cat-pill" onclick="filterProducts('chatgpt', event)">ChatGPT Plus</button>
        <button class="cat-pill pro-glow" onclick="filterProducts('chatgpt', event)"><i
                class="fa-solid fa-sparkles text-info"></i> Claude AI Chính Hãng</button>
        <button class="cat-pill" onclick="filterProducts('chatgpt', event)">Grok AI Pro</button>
        <button class="cat-pill" onclick="filterProducts('github', event)">IntelliJ IDEA</button>
        <button class="cat-pill" onclick="filterProducts('github', event)">Tài khoản GitHub Copilot Pro</button>
        <button class="cat-pill" onclick="filterProducts('github', event)">Tài khoản JetBrains</button>
        <button class="cat-pill" onclick="filterProducts('netflix', event)">Tài khoản figma pro 1 năm</button>
        <button class="cat-pill pro-glow" onclick="filterProducts('chatgpt', event)"><i
                class="fa-solid fa-sparkles text-success"></i> Cursor Pro</button>
    </div>

    <div class="row g-4">
        <?php foreach ($products as $index => $product): ?>
            <div class="col-6 col-md-4 col-lg-3 product-item fade-in-element"
                style="animation-delay: <?= ($index + 1) * 0.1 ?>s;"
                data-category="<?= htmlspecialchars($product['category_slug']) ?>">
                <div class="card product-card position-relative h-100"
                    onclick="window.location.href='<?php echo url('index.php?action=productDetail&id=' . htmlspecialchars($product['id'])); ?>'" style="cursor: pointer;">
                    <?php if (!empty($product['badge'])): ?>
                        <span class="badge-hot"><?= htmlspecialchars($product['badge']) ?></span>
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($product['category']) ?>">
                    <div class="card-body d-flex flex-column p-4">
                        <h3 class="product-title"><?= htmlspecialchars($product['title']) ?></h3>
                        <p class="text-muted small mb-3"><i
                                class="fa-solid <?= htmlspecialchars($product['feature_icon']) ?> me-1"></i>
                            <?= htmlspecialchars($product['feature_text']) ?></p>
                        <div class="mt-auto">
                            <p class="product-price mb-3"><?= htmlspecialchars($product['price']) ?></p>
                            <div class="product-actions" onclick="event.stopPropagation();">
                                <button class="btn btn-buy shadow-sm"
                                    onclick="buyNow('<?= htmlspecialchars($product['category']) ?>')">Mua ngay</button>
                                <button class="btn btn-cart-icon shadow-sm"
                                    onclick="addToCart('<?= htmlspecialchars($product['category']) ?>')" title="Thêm">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="blog-section" style="display: none;">
    <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom border-light">
        <div class="tab-nav">
            <button class="tab-btn" onclick="switchTab('products', event)">Sản Phẩm</button>
            <button class="tab-btn active" onclick="switchTab('blog', event)">Tạp Chí (Blog)</button>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-12 col-md-6 col-lg-4 fade-in-element">
            <div class="blog-card h-100">
                <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=400&h=200"
                    class="blog-img" alt="Blog">
                <div class="blog-content d-flex flex-column h-100">
                    <div class="blog-date">15 Th05, 2026</div>
                    <h3 class="blog-title">Tối Ưu Hóa Trải Nghiệm AI Trong Công Việc</h3>
                    <p class="blog-desc flex-grow-1">Bí quyết sử dụng các dòng lệnh (prompt) chuẩn mực để tiết kiệm 50%
                        thời gian làm việc mỗi ngày...</p>
                    <a href="<?php echo url('index.php?action=blogDetail'); ?>" class="read-more">Xem chi tiết <i
                             class="fa-solid fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4 fade-in-element" style="animation-delay: 0.1s;">
            <div class="blog-card h-100">
                <img src="https://images.unsplash.com/photo-1516321497487-e288fb19713f?auto=format&fit=crop&q=80&w=400&h=200"
                    class="blog-img" alt="Blog">
                <div class="blog-content d-flex flex-column h-100">
                    <div class="blog-date">12 Th05, 2026</div>
                    <h3 class="blog-title">Xu Hướng Giải Trí Số Định Dạng 4K</h3>
                    <p class="blog-desc flex-grow-1">Đánh giá chất lượng hình ảnh từ các nền tảng streaming hàng đầu và
                        lý do bạn nên nâng cấp phần cứng...</p>
                    <a href="<?php echo url('index.php?action=blogDetail'); ?>" class="read-more">Xem chi tiết <i
                             class="fa-solid fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4 fade-in-element" style="animation-delay: 0.2s;">
            <div class="blog-card h-100">
                <img src="https://images.unsplash.com/photo-1618401479427-c8ef9465fbe1?auto=format&fit=crop&q=80&w=400&h=200"
                    class="blog-img" alt="Blog">
                <div class="blog-content d-flex flex-column h-100">
                    <div class="blog-date">10 Th05, 2026</div>
                    <h3 class="blog-title">Dev Công Cụ Hay Lập Trình Viên Thực Thụ?</h3>
                    <p class="blog-desc flex-grow-1">Sự can thiệp của AI vào quá trình viết code đang thay đổi định
                        nghĩa về ngành công nghệ phần mềm...</p>
                    <a href="index.php?action=blogDetail" class="read-more">Xem chi tiết <i
                            class="fa-solid fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popup Khách hàng vừa mua (Chỉ hiển thị ở trang chủ) -->
<div id="recent-purchase-popup" class="recent-purchase-popup">
    <div class="progress-bar-container">
        <div id="purchase-progress-bar" class="purchase-progress-bar"></div>
    </div>
    <div class="popup-content p-3 position-relative">
        <button class="btn-close-popup" onclick="closePurchasePopup()"><i class="fa-solid fa-xmark"></i></button>

        <div class="d-flex align-items-center mb-3">
            <span class="badge bg-success rounded-pill me-2 px-2 py-1"><i class="fa-solid fa-sparkles me-1"></i> Vừa
                mua</span>
            <span class="text-muted small"><i class="fa-regular fa-circle-check text-success"></i> Đã xác minh</span>
        </div>

        <div class="d-flex mb-3 align-items-center">
            <div id="popup-avatar-bg" class="avatar-circle me-3 position-relative flex-shrink-0"
                style="background-color: #a855f7;">
                <span id="popup-avatar-text">Q</span>
                <i class="fa-solid fa-circle-check text-success position-absolute bottom-0 end-0 bg-white rounded-circle"
                    style="font-size: 0.8rem; transform: translate(2px, 2px);"></i>
            </div>
            <div>
                <div id="popup-customer-name" class="fw-bold text-dark" style="font-size: 1.05rem;">Quốc Anh Đặng</div>
                <div class="small text-muted"><i class="fa-solid fa-bag-shopping me-1"></i> vừa mua thành công</div>
            </div>
        </div>

        <div class="bg-success bg-opacity-10 rounded-3 p-3 mb-3">
            <div id="popup-product-name" class="fw-bold text-dark small text-truncate" style="max-width: 250px;">Tài
                khoản Cursor Pro 20$ - 6 Ngày...</div>
            <div class="small mt-2"><span id="popup-product-price" class="text-success fw-bold fs-6">89.000 đ</span>
                <span class="text-muted ms-2">&bull; <span id="popup-time">17 phút trước</span></span>
            </div>
        </div>

        <div class="small text-muted">
            <i class="fa-solid fa-location-dot me-2"></i> <span id="popup-location">Đà Nẵng</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const popup = document.getElementById('recent-purchase-popup');
        const progressBar = document.getElementById('purchase-progress-bar');

        if (!popup) return;

        // Danh sách dữ liệu giả lập khách hàng mua hàng
        const fakeOrders = [
            { name: 'Quốc Anh Đặng', initial: 'Q', bg: '#a855f7', product: 'Tài khoản Cursor Pro 20$ - Dùng Chung', price: '89.000 đ', time: 'Vài giây trước', location: 'Đà Nẵng' },
            { name: 'Trần Minh Tuấn', initial: 'T', bg: '#3b82f6', product: 'ChatGPT Plus 1 Tháng - Gia hạn', price: '450.000 đ', time: '2 phút trước', location: 'Hà Nội' },
            { name: 'Lê Hoàng Yến', initial: 'Y', bg: '#ec4899', product: 'Netflix Premium 4K - 1 Tháng', price: '85.000 đ', time: '15 phút trước', location: 'TP. Hồ Chí Minh' },
            { name: 'Phạm Đức Bảo', initial: 'P', bg: '#f59e0b', product: 'Github Copilot 1 Năm - Gói Dev', price: '150.000 đ', time: '30 phút trước', location: 'Hải Phòng' },
            { name: 'Nguyễn Thị Lan', initial: 'N', bg: '#10b981', product: 'YouTube Premium 1 Tháng', price: '25.000 đ', time: '1 giờ trước', location: 'Cần Thơ' }
        ];

        let orderIndex = 0;
        let hideTimeout = null;
        let nextTimeout = null;

        function showNextOrder() {
            // Lấy thông tin order tiếp theo (xoay vòng)
            const order = fakeOrders[orderIndex];
            orderIndex = (orderIndex + 1) % fakeOrders.length;

            // Cập nhật giao diện popup
            document.getElementById('popup-avatar-bg').style.backgroundColor = order.bg;
            document.getElementById('popup-avatar-text').innerText = order.initial;
            document.getElementById('popup-customer-name').innerText = order.name;
            document.getElementById('popup-product-name').innerText = order.product;
            document.getElementById('popup-product-price').innerText = order.price;
            document.getElementById('popup-time').innerText = order.time;
            document.getElementById('popup-location').innerText = order.location;

            // Reset thanh năng lượng
            progressBar.style.transition = 'none';
            progressBar.style.width = '100%';

            // Hiện popup
            popup.classList.add('show');

            // Chạy thanh năng lượng 5 giây
            setTimeout(() => {
                progressBar.style.transition = 'width 5s linear';
                progressBar.style.width = '0%';
            }, 50);

            // Ẩn sau 5 giây
            hideTimeout = setTimeout(() => {
                window.closePurchasePopup(false);
            }, 5000);
        }

        // Lần đầu tiên hiện sau 3 giây
        nextTimeout = setTimeout(showNextOrder, 3000);

        // Gắn hàm close ra global để nút X gọi được (nếu người dùng bấm, truyền ngầm định undefined = true)
        window.closePurchasePopup = function (isManual = true) {
            popup.classList.remove('show');
            progressBar.style.width = '0%';
            
            // Xóa các luồng chạy cũ để bắt đầu luồng tính giờ mới
            clearTimeout(hideTimeout);
            clearTimeout(nextTimeout);

            // Nếu người dùng chủ động tắt bằng nút X (isManual = true), ngưng 20s
            // Nếu tự động tắt (isManual = false), thời gian random 7-15s
            const delay = isManual ? 20000 : (Math.floor(Math.random() * 8000) + 7000);
            
            nextTimeout = setTimeout(showNextOrder, delay);
        };
    });
</script>