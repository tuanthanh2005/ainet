<div id="home-section" style="display: <?php echo ($tab === 'home') ? 'block' : 'none'; ?>;">
    <!-- Hero / Intro Section -->
    <div class="row align-items-center g-5 py-5 mb-5 rounded-4 position-relative overflow-hidden fade-in-element" style="background: rgba(255,255,255,0.45); border: 1px solid var(--border-color); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); margin-left: 0; margin-right: 0;">
        <div class="col-lg-7 text-start ps-4 ps-md-5">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill fw-bold" style="letter-spacing:1px; font-size:0.75rem;"><i class="fa-solid fa-sparkles me-1"></i> HỆ SINH THÁI TÀI KHOẢN PREMIUM</span>
            <h1 class="display-5 fw-bold text-dark mb-3 lh-sm">Sở Hữu Tài Khoản AI <br><span class="text-gradient fw-extrabold" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Chính Hãng, Giá Rẻ</span></h1>
            <p class="lead text-muted mb-4 fs-6" style="line-height:1.7;">
                Chào mừng bạn đến với <strong>AI CỦA TÔI</strong> - nền tảng hàng đầu cung cấp các tài khoản Premium (ChatGPT Plus, Claude Pro, Midjourney, YouTube Premium, GitHub Copilot...) tự động 24/7. Uy tín, an toàn, kích hoạt ngay lập tức với chế độ bảo hành 1 đổi 1 trọn gói.
            </p>
            <div class="d-flex flex-wrap gap-3">
                <a href="<?php echo url('index.php?tab=products'); ?>" class="btn btn-buy px-4 py-2.5 fs-6 shadow-sm"><i class="fa-solid fa-store me-2"></i>Xem sản phẩm</a>
                <a href="<?php echo Url::about(); ?>" class="btn btn-outline-dark px-4 py-2.5 fs-6" style="border-radius: 8px;"><i class="fa-solid fa-circle-info me-2"></i>Về chúng tôi</a>
            </div>
        </div>
        <div class="col-lg-5 text-center pe-4 pe-md-5 d-none d-lg-block">
            <img src="<?php echo url('assets/images/gemini_share.png'); ?>" class="img-fluid rounded-4 shadow-sm" alt="AI Của Tôi" style="max-height: 280px; object-fit: cover; border: 1px solid var(--border-color);">
        </div>
    </div>

    <!-- Stats Counter Section -->
    <div class="row g-4 mb-5 text-center fade-in-element" style="animation-delay: 0.1s;">
        <div class="col-6 col-md-3">
            <div class="p-4 rounded-4" style="background: rgba(255,255,255,0.6); border: 1px solid var(--border-color); backdrop-filter: blur(8px);">
                <div class="fs-2 fw-extrabold text-primary" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">50,000+</div>
                <div class="small text-muted fw-bold mt-1">Khách Hàng Tin Dùng</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-4 rounded-4" style="background: rgba(255,255,255,0.6); border: 1px solid var(--border-color); backdrop-filter: blur(8px);">
                <div class="fs-2 fw-extrabold text-primary" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">120,000+</div>
                <div class="small text-muted fw-bold mt-1">Đơn Hàng Thành Công</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-4 rounded-4" style="background: rgba(255,255,255,0.6); border: 1px solid var(--border-color); backdrop-filter: blur(8px);">
                <div class="fs-2 fw-extrabold text-primary" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">99.9%</div>
                <div class="small text-muted fw-bold mt-1">Xử Lý Tự Động 24/7</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-4 rounded-4" style="background: rgba(255,255,255,0.6); border: 1px solid var(--border-color); backdrop-filter: blur(8px);">
                <div class="fs-2 fw-extrabold text-primary" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">4.9/5 ★</div>
                <div class="small text-muted fw-bold mt-1">Đánh Giá Hài Lòng</div>
            </div>
        </div>
    </div>

    <!-- Outstanding Features Section -->
    <div class="mb-5 text-center fade-in-element" style="animation-delay: 0.2s;">
        <h2 class="fw-bold mb-2">Cam Kết Vàng Tại AI CỦA TÔI</h2>
        <p class="text-muted small mb-4">Sự hài lòng và độ tin cậy của khách hàng là ưu tiên hàng đầu của chúng tôi</p>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="p-4 rounded-4 h-100 text-start bg-white" style="border: 1px solid var(--border-color); transition: all 0.3s ease;">
                    <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3 mb-3" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-bolt fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Giao Hàng 5 Giây</h5>
                    <p class="text-muted small mb-0">Hệ thống xử lý thanh toán tự động, gửi tài khoản qua email/màn hình ngay sau khi giao dịch thành công.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 rounded-4 h-100 text-start bg-white" style="border: 1px solid var(--border-color); transition: all 0.3s ease;">
                    <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3 mb-3" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-shield-halved fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Bảo Hành Uy Tín</h5>
                    <p class="text-muted small mb-0">Cam kết bảo hành 1 đổi 1 hoặc hoàn tiền tương ứng nếu xảy ra lỗi trong suốt thời gian sử dụng gói dịch vụ.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 rounded-4 h-100 text-start bg-white" style="border: 1px solid var(--border-color); transition: all 0.3s ease;">
                    <div class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-3 mb-3" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-tags fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Giá Tốt Nhất</h5>
                    <p class="text-muted small mb-0">Chúng tôi tối ưu hóa chi phí để mang lại mức giá rẻ hơn đến 70% so với việc mua trực tiếp từ nhà phát triển.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 rounded-4 h-100 text-start bg-white" style="border: 1px solid var(--border-color); transition: all 0.3s ease;">
                    <div class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-3 mb-3" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-headset fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Hỗ Trợ Tận Tâm</h5>
                    <p class="text-muted small mb-0">Đội ngũ kỹ thuật viên am hiểu công nghệ sẵn sàng tư vấn và sửa lỗi trực tuyến qua Zalo / Telegram.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Highlights Brands Section -->
    <div class="mb-5 text-center fade-in-element" style="animation-delay: 0.3s;">
        <h2 class="fw-bold mb-2">Đối Tác & Dịch Vụ Phổ Biến</h2>
        <p class="text-muted small mb-4">Các bộ công cụ AI hàng đầu thế giới được nhiều doanh nghiệp sử dụng</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo Url::category($cat['seo_slug'] ?: $cat['slug']); ?>" class="text-decoration-none d-flex align-items-center gap-2 px-4 py-3 rounded-4 shadow-sm border border-light bg-white hover-up" style="transition: all 0.3s ease;">
                    <?php if ($cat['icon']): ?>
                        <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> <?= htmlspecialchars($cat['icon_color'] ?: 'text-primary') ?> fs-5"></i>
                    <?php endif; ?>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="mb-5 fade-in-element" style="animation-delay: 0.4s;">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-2">Khách Hàng Nói Về Chúng Tôi</h2>
            <p class="text-muted small">Ý kiến đóng góp thực tế từ những chuyên gia, lập trình viên sử dụng dịch vụ</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(255,255,255,0.7); border: 1px solid var(--border-color); backdrop-filter: blur(8px);">
                    <div>
                        <div class="text-warning mb-3">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="text-muted small italic mb-3">"Hệ thống mua hàng rất thông minh. Tôi thanh toán QR ngân hàng xong chỉ sau đúng 5 giây là email đã nhận được tài khoản ChatGPT Plus. Tiết kiệm thời gian mà giá lại rất rẻ so với tự nâng cấp bằng thẻ ngoại."</p>
                    </div>
                    <div class="d-flex align-items-center mt-3 border-top pt-3">
                        <img src="https://ui-avatars.com/api/?name=Nguyen+Tuan&background=6366f1&color=fff" class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                        <div>
                            <div class="fw-bold text-dark small">Nguyễn Anh Tuấn</div>
                            <div class="text-muted smaller">Lập trình viên Fullstack</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(255,255,255,0.7); border: 1px solid var(--border-color); backdrop-filter: blur(8px);">
                    <div>
                        <div class="text-warning mb-3">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="text-muted small italic mb-3">"Làm Content Creator rất cần Claude Pro và Midjourney để lên bài và thiết kế ảnh. Nhờ AI CỦA TÔI, tôi đã đăng ký gói combo rất mượt mà. Support hỗ trợ nhiệt tình qua Zalo ngay cả nửa đêm."</p>
                    </div>
                    <div class="d-flex align-items-center mt-3 border-top pt-3">
                        <img src="https://ui-avatars.com/api/?name=Phan+Linh&background=a855f7&color=fff" class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                        <div>
                            <div class="fw-bold text-dark small">Phan Khánh Linh</div>
                            <div class="text-muted smaller">Content Team Lead</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(255,255,255,0.7); border: 1px solid var(--border-color); backdrop-filter: blur(8px);">
                    <div>
                        <div class="text-warning mb-3">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="text-muted small italic mb-3">"Đã sử dụng ở đây hơn 6 tháng, gia hạn tài khoản YouTube Premium và Github Copilot định kỳ. Chưa bao giờ bị lỗi mất gói giữa chừng, nếu có vấn đề gì được đổi tài khoản mới ngay lập tức."</p>
                    </div>
                    <div class="d-flex align-items-center mt-3 border-top pt-3">
                        <img src="https://ui-avatars.com/api/?name=Tran+Minh&background=0ea5e9&color=fff" class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                        <div>
                            <div class="fw-bold text-dark small">Trần Đức Minh</div>
                            <div class="text-muted smaller">Sinh viên Kỹ thuật</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Blog posts for home tab credibility -->
    <?php if (!empty($blogs)): ?>
        <div class="mb-5 fade-in-element" style="animation-delay: 0.5s;">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Cập Nhật Tin Tức & Hướng Dẫn</h2>
                    <p class="text-muted small mb-0">Các kiến thức hữu ích và mẹo sử dụng công cụ AI hiệu quả</p>
                </div>
                <a href="<?php echo url('index.php?tab=blog'); ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach (array_slice($blogs, 0, 3) as $blog): ?>
                    <?php
                        $blogDate  = !empty($blog['created_at']) ? date('d/m/Y', strtotime($blog['created_at'])) : '';
                        $blogTitle = $blog['title'] ?? '';
                        $blogImage = $blog['image'] ?? '';
                    ?>
                    <div class="col-12 col-md-4">
                        <a href="<?= htmlspecialchars(Url::blog($blog)) ?>" class="text-decoration-none text-reset">
                            <div class="blog-card h-100 shadow-sm border bg-white" style="border-radius:12px; overflow:hidden; transition:all 0.3s ease;">
                                <div style="height: 160px; overflow:hidden;">
                                    <img src="<?= htmlspecialchars($blogImage) ?>" class="w-100 h-100" style="object-fit:cover; transition:all 0.5s ease;" alt="<?= htmlspecialchars($blogTitle) ?>">
                                </div>
                                <div class="p-3">
                                    <?php if ($blogDate): ?>
                                        <div class="text-muted small mb-1"><i class="fa-regular fa-clock me-1"></i><?= htmlspecialchars($blogDate) ?></div>
                                    <?php endif; ?>
                                    <h3 class="fw-bold mb-0 text-dark" style="font-size:1rem; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; height:2.8rem;"><?= htmlspecialchars($blogTitle) ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="products-section" style="display: <?php echo ($tab === 'products') ? 'block' : 'none'; ?>;">
    <!-- Category Pill Menu -->
    <div class="category-menu-wrapper fade-in-element" style="animation-delay: 0.1s;">
        <?php
        $currentCat = $categorySlug ?? ($_GET['category'] ?? '');
        $qParam = !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '';
        $sortParam = !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
        ?>
        <a href="<?php echo url('index.php?tab=products' . $qParam . $sortParam); ?>"
           class="cat-pill text-decoration-none <?php echo (empty($currentCat) || $currentCat === 'all') ? 'active' : ''; ?>">Tất Cả</a>
        <?php foreach ($categories as $cat): ?>
            <?php $activeSlug = $cat['seo_slug'] ?: $cat['slug']; ?>
            <a href="<?php echo Url::category($activeSlug) . ($qParam || $sortParam ? '?' . ltrim($qParam . $sortParam, '&') : ''); ?>"
               class="cat-pill text-decoration-none <?= $cat['is_pro'] ? 'pro-glow' : '' ?> <?php echo ($currentCat === $cat['slug'] || $currentCat === $cat['seo_slug']) ? 'active' : ''; ?>">
                <?php if ($cat['icon']): ?>
                    <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> <?= htmlspecialchars($cat['icon_color'] ?: 'text-primary') ?>"></i>
                <?php endif; ?>
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row g-4" id="product-list" data-page-size="12">
        <?php foreach ($products as $index => $product): ?>
            <div class="col-6 col-md-4 col-lg-3 product-item"
                 data-category="<?= htmlspecialchars($product['category_slug'] ?? '') ?>">
                <div class="card product-card position-relative h-100" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>">
                    <?php if (!empty($product['badge'])): ?>
                        <span class="badge-hot"><?= htmlspecialchars($product['badge'] ?? '') ?></span>
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars($product['image'] ?? '') ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($product['category'] ?? '') ?>">
                    <div class="card-body d-flex flex-column p-4">
                        <h3 class="product-title mb-1">
                            <a href="<?= htmlspecialchars(Url::product($product)) ?>" class="stretched-link text-decoration-none text-dark">
                                <?= htmlspecialchars($product['title'] ?? '') ?>
                            </a>
                        </h3>
                        <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.75rem;">
                            <div class="text-warning">
                                <?php 
                                    $rating = $product['rating'] ?? 5;
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= floor($rating)) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        } elseif ($i - 0.5 == $rating) {
                                            echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    }
                                ?>
                            </div>
                            <span class="text-muted sold-text">Đã bán <?= number_format($product['sold_count'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <p class="text-muted small mb-3"><i
                                class="fa-solid <?= htmlspecialchars($product['feature_icon'] ?? 'fa-circle-check') ?> me-1"></i>
                            <?= htmlspecialchars($product['feature_text'] ?? '') ?></p>
                        <div class="mt-auto">
                            <?php
                                $cardOrig = (float) ($product['options'][0]['original_price'] ?? 0);
                                $cardPrice = (float) ($product['options'][0]['price'] ?? $product['price'] ?? 0);
                                $cardHasDiscount = $cardOrig > $cardPrice && $cardPrice > 0;
                                $cardOff = $cardHasDiscount ? round((1 - $cardPrice / $cardOrig) * 100) : 0;
                            ?>
                            <?php if ($cardHasDiscount): ?>
                                <div class="d-flex align-items-baseline gap-2 mb-1">
                                    <p class="product-price mb-0"><?= number_format($cardPrice, 0, ',', '.') ?>đ</p>
                                    <span class="badge bg-danger"><?= '-' . $cardOff . '%' ?></span>
                                </div>
                                <p class="text-muted text-decoration-line-through small mb-3" style="line-height:1;">
                                    <?= number_format($cardOrig, 0, ',', '.') ?>đ
                                </p>
                            <?php else: ?>
                                <p class="product-price mb-3"><?= number_format($cardPrice, 0, ',', '.') ?>đ</p>
                            <?php endif; ?>
                            <div class="product-actions position-relative" style="z-index: 2;">
                                <a href="<?= url('index.php?action=checkoutPage&product_id=' . urlencode($product['id']) . '&variant_idx=0') ?>" 
                                   class="btn btn-buy shadow-sm" data-auth-required="true">Mua ngay</a>
                                <a href="<?= url('index.php?action=addToCart&id=' . urlencode($product['id'])) ?>" 
                                   class="btn btn-cart-icon shadow-sm" title="Thêm">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <?php
        $catParam = !empty($currentCat) ? '&category=' . urlencode($currentCat) : '';
        $sortParam = !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
        $qParam = !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '';
        $pageUrl = function($p) use ($catParam, $qParam, $sortParam) {
            return url('index.php?tab=products' . $catParam . $qParam . $sortParam . '&page=' . $p . '#products-section');
        };
        ?>
        <div class="d-flex justify-content-center mt-5 mb-3">
            <nav aria-label="Product pagination">
                <ul class="pagination pagination-md shadow-sm border rounded-pill overflow-hidden bg-white px-2 py-1 mb-0" style="gap:4px; list-style: none;">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link border-0 rounded-circle text-dark d-flex align-items-center justify-content-center" 
                               style="width:36px;height:36px;" href="<?= $pageUrl($page - 1) ?>">
                                <i class="fa-solid fa-chevron-left small"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link border-0 rounded-circle d-flex align-items-center justify-content-center <?= $i === $page ? 'bg-dark text-white fw-bold' : 'text-dark' ?>" 
                               style="width:36px;height:36px;" href="<?= $pageUrl($i) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link border-0 rounded-circle text-dark d-flex align-items-center justify-content-center" 
                               style="width:36px;height:36px;" href="<?= $pageUrl($page + 1) ?>">
                                <i class="fa-solid fa-chevron-right small"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div id="product-empty" class="text-center py-5 text-muted">
            <i class="fa-regular fa-folder-open fs-1 opacity-25 mb-3 d-block"></i>
            <p class="mb-0">Không có sản phẩm nào trong danh mục này.</p>
        </div>
    <?php endif; ?>
</div>

<div id="blog-section" style="display: <?php echo ($tab === 'blog') ? 'block' : 'none'; ?>;">
    <div class="row g-4">
        <?php if (empty($blogs)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="fa-regular fa-newspaper fs-1 opacity-25 mb-3 d-block"></i>
                <p class="mb-0">Chưa có bài viết nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($blogs as $index => $blog): ?>
                <?php
                    $blogDate  = !empty($blog['created_at']) ? date('d/m/Y', strtotime($blog['created_at'])) : '';
                    $blogTitle = $blog['title'] ?? '';
                    $blogImage = $blog['image'] ?? '';
                ?>
                <div class="col-12 col-md-6 col-lg-4 fade-in-element" style="animation-delay: <?= $index * 0.1 ?>s;">
                    <a href="<?= htmlspecialchars(Url::blog($blog)) ?>" class="text-decoration-none text-reset">
                        <div class="blog-card h-100 bg-white shadow-sm border" style="border-radius:12px; overflow:hidden;">
                            <img src="<?= htmlspecialchars($blogImage) ?>" class="blog-img" alt="<?= htmlspecialchars($blogTitle) ?>">
                            <div class="blog-content p-3">
                                <?php if ($blogDate): ?>
                                    <div class="blog-date small text-muted mb-1"><?= htmlspecialchars($blogDate) ?></div>
                                <?php endif; ?>
                                <h3 class="blog-title mb-0 fw-bold fs-6 text-dark"><?= htmlspecialchars($blogTitle) ?></h3>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
    const fakeOrders = <?php echo json_encode($recentOrders); ?>;
</script>